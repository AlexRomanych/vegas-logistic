<?php

namespace App\Services\Manufacture;


use App\Classes\EndPointStaticRequestAnswer;
use App\Classes\ManufactureDayAndChange;
use App\Classes\CuttingTimeLabor;
use App\Models\Logs\EventLog;
use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskLine;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskStatus;
use App\Models\Models\Model;
use App\Models\Models\ModelConstruct;
use App\Models\Order\Order;
use App\Services\BusinessProcessesService;
use App\Services\ModelsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CuttingService
{

    /**
     * ___ Создать СЗ для Пошива из основного Заказа
     * @param int $orderId             ID основного Заказа
     * @param string|null $plannedDate Дата планируемого выполнения СЗ - должна быть либо дата, либо смещение, приоритет - дата
     * @return CuttingTask|null
     * @noinspection DuplicatedCode
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function createCuttingTaskFromOrderId(
        int $orderId,
        string|null $plannedDate = null,
        bool $calculateCut = false,
    ): ?CuttingTask {
        //try {

        $getConstruct      = fn($q) => $q->select([CODE_1C, 'name', 'model_code_1c', 'model_name']);
        $getConstructItems = fn($q) => $q->select([
            'id',
            'construct_code_1c',
            'material_code_1c',
            'material_code_1c_copy',
            'material_name',
            'material_unit',
            'detail',
            'detail_height',
            'procedure_code_1c',
            'procedure_code_1c_copy',
            'procedure_name',
            'amount',
            'position',
        ]);

        // __ Проверяем на существование заказа
        // __ TODO Доработать выборку данных (убрать не нужные)
        $order = Order::query()
            ->select(['id', 'load_at', 'client_id', 'elements_type', 'order_no_origin'])
            ->with([

                // __ 1. От строк берем только ключи
                'lines'                                                                  => fn($q) => $q->select(
                    ['id', 'order_id', 'size', 'model_code_1c', 'model_name', 'amount', 'construct_code_1c', 'construct_add_code_1c', 'textile']
                ),

                // __ Это Спецификация, который приходит по коду привязанной спецификации в строке Заказа
                // __ Если Чехол, то прокатит, и берем ее
                // __ Жадно загружаем Спецификации по коду привязки Спецификации
                'lines.specification'                                                    => $getConstruct,
                'lines.specificationAdd'                                                 => $getConstruct,
                //'lines.specification' => fn($q) => $q->select([CODE_1C, 'name', 'model_code_1c', 'model_name']), // __ Жадно загружаем Спецификации по коду привязки Спецификации
                // __ Жадно загружаем состав Спецификации
                'lines.specification.constructItems'                                     => $getConstructItems,
                'lines.specificationAdd.constructItems'                                  => $getConstructItems,

                // __ Если это МЭ, то по найденной Спецификации выходим на модель и подгружаем его Чехол
                // __ Если Модель - это Чехол, который был найден выше, то Cover, по идее у него не будет
                'lines.specification.model'                                              => fn($q) => $q->select([CODE_1C, 'cover_code_1c']),
                'lines.specification.model.cover'                                        => fn($q) => $q->select([CODE_1C, 'cover_code_1c']),
                'lines.specification.model.cover.constructs'                             => $getConstruct,
                'lines.specification.model.cover.constructs.constructItems'              => $getConstructItems,

                // __ Тут ситуация, когда Спецификация в строке Заказа указана на левую, например, тендерную Спецификацию,
                // __ у которой нет регулярной модели, причем на МЭ.
                // __ Тогда пробуем найти Спецификацию на Чехол по Названию Чехла
                'lines.specification.constructItems.semiproductConstruct'                => $getConstruct,
                'lines.specification.constructItems.semiproductConstruct.constructItems' => $getConstructItems,
                //'lines.specification.model.cover.constructs.constructItems.semiproductConstruct',
                //'lines.specification.model.cover.constructs.constructItems.semiproductConstruct.constructItems',

                // __ Подгружаем клиента
                'client'
            ])
            ->find($orderId);


        if (!$order) {
            return null;
        }

        $orderDebug = $order->toArray();


        // __ Плучаем Расход

        $groupedPivotRecordsExpense = DB::table('order_line_material_pivot as pivot')
            ->join('order_lines as lines', 'lines.id', '=', 'pivot.order_line_id')
            ->where('lines.order_id', $orderId)
            ->whereIn('pivot.detail', [ModelConstruct::DETAIL_CONSTRUCT_PANEL_NAME, ModelConstruct::DETAIL_CONSTRUCT_SIDE_NAME,])

            // Перечисляем только те поля, которые нам реально нужны:
            ->select([
                'pivot.order_line_id', // ⚠️ КРИТИЧЕСКИ ВАЖНО для последующего groupBy!
                'pivot.material_code_1c',
                'pivot.material_name_expense',
                'pivot.detail',
                'pivot.position',
                'pivot.expense',
                'pivot.rest',
                // 'pivot.quantity' — например, какое-то еще твое поле
            ])
            ->orderBy('pivot.position', 'asc')
            ->get()
            ->groupBy('order_line_id')
            ->toArray();


        // __ Получаем плановую дату
        if (!(is_null($plannedDate) || $plannedDate === '')) {
            $plannedDate = normalizeToCarbon($plannedDate);
        } else {
            // __ Получаем смещение в днях для Пошива
            $offset      = BusinessProcessesService::getDateOffsetForOrderMovingProcessByNodeIdAndClientId(CUTTING_NODE_ID, $order->client->id);
            $plannedDate = normalizeToCarbon($order->load_at)->addDays($offset);
        }

        //$createdTask = null;
        // DB::transaction(function () use ($order, $plannedDate, &$createdTask) {

        // __ Создаем СЗ
        $createdTask = CuttingTask::query()->create([
            'action_at' => $plannedDate,
            'order_id'  => $order->id,
            'position'  => self::getCuttingTaskLastPositionInDay($plannedDate) + 1, // __ Получаем позицию для нового СЗ
        ]);
        if (!$createdTask) {
            throw new Exception('Failed to create CuttingTask');
        }

        // __ Создаем контент (строки) СЗ
        $position = 1;
        foreach ($order->lines as $line) {
            // __ Определяем дефолтные детальки в случае ошибки
            $defaultDetails = [
                'panels' => [
                    [
                        'name'     => $line->textile,
                        'code'     => null,
                        'type'     => ModelConstruct::PANEL_NAME,
                        'position' => 0,
                        'amount'   => 2,
                    ]
                ]
            ];


            $lineArray = $line->toArray();
            if ($line->id === 27802) {
                $a = 0;
            }


            // IMPORTANT Подумать над логикой определения рабочей Спецификации
            // __ Определяем рабочую спецификацию
            // __ Основная идея - найти Чехол
            // __ Пока выбираем, возможно потом будем складывать эти спецификации
            $targetConstruct = null;


            if (isset($line->specificationAdd) && ModelsService::isElementCoverByConstruct($line->specificationAdd)) {
                // __ Идем сначала в Дополнительную привязанную Спецификацию и стотрим ее на Чехол
                $targetConstruct = $line->specificationAdd;
            } elseif (isset($line->specificationAdd) && !ModelsService::isElementCoverByConstruct($line->specificationAdd)) {
                // __ Логично предполагаем, что это МЭ
                // __ Пытаемся его вытащить
                $targetConstruct = self::getModelConstructCover($line->specificationAdd);
            }

            if (!$targetConstruct) {
                // __ Если ничего не нашли, проделываем такой же фокус с привязанной Спецификацию
                if (isset($line->specification) && ModelsService::isElementCoverByConstruct($line->specification)) {
                    // __ Идем сначала в Дополнительную привязанную Спецификацию и стотрим ее на Чехол
                    $targetConstruct = $line->specification;
                } elseif (isset($line->specification) && !ModelsService::isElementCoverByConstruct($line->specification)) {
                    // __ Логично предполагаем, что это МЭ
                    // __ Пытаемся его вытащить
                    $targetConstruct = self::getModelConstructCover($line->specification);

                    // __ Если не находим эту Спецификацию Чехла по Спецификации МЭ,
                    if (!$targetConstruct) {
                        if (isset($line->specification->model->cover->constructs) &&
                            !is_null($line->specification->model->cover->constructs) &&
                            count($line->specification->model->cover->constructs) > 0
                        ) {
                            $targetConstruct = $line->specification->model->cover->constructs[0];
                        }
                    }
                }
            }

            $targetConstructArray = $targetConstruct->toArray();
            if ($line->id === 27802) {
                $a = 0;
            }

            //if (isset($line->specification->model->cover->constructs) &&
            //    !is_null($line->specification->model->cover->constructs) &&
            //    count($line->specification->model->cover->constructs) > 0
            //) {
            //    // __ Если пройдена цепочка от кода спецификации в строке Заказа до Привязанного Чехла
            //    // __ Значит в Строке указан код Спецификации Матраса, и по цепочке связей находим Чехол
            //    $targetConstruct = $line->specification->model->cover->constructs[0];
            //} elseif (isset($line->specification)) {
            //    // __ Если подгрузилась Спецификация из поля привязки в строке Заказа
            //    if (CuttingService::hasModelConstructCover($line->specification)) {
            //        // __ и в ней есть Чехол (например, тендерная Спецификация на МЭ), берем этот Чехол
            //        $targetConstruct = CuttingService::getModelConstructCover($line->specification);
            //    } else {
            //        // __ или берем саму Спецификацию, которая подгрузилась. Вероятно она будет Чехлом
            //        $targetConstruct = $line->specification;
            //    }
            //}

            // __ Маяк того, что где-то что-то пошло не так
            // __ Будем создавать по этому маяку дефолтное поведение
            $hasError = false;

            // __ Если не находим Спецификацию
            if (!$targetConstruct) {
                // __ Пишем в EventLog Ошибку
                $eventLog          = new EventLog();
                $eventLog->level   = EventLog::LEVEL_WARNING;
                $eventLog->target  = EventLog::TARGET_CUTTING_TASK;
                $eventLog->message = 'Не найдена спецификация';
                $eventLog->context = [
                    'spec_code_1c' => $line->construct_code_1c,
                    'model_name'   => $line->model_name,
                    'order'        => $order->order_no_origin,
                ];
                $eventLog->save();

                $hasError = true;
                //continue;

                $constructDetails = $defaultDetails;
            } else {
                // __ Если это расчетная модель (AVERAGE), то ставим позицию 0
                // if ($order->lines->count() === 1 && ModelsService::isElementAverage($line->model_code_1c)) {
                //     $position = 0;
                // }

                $debugLine            = $line->toArray();
                $targetConstructDebug = $targetConstruct?->toArray();

                $constructDetails = self::getCuttingDetailsByConstruct($targetConstruct);

                // __ Нет деталей Чехла вообще
                if (count($constructDetails) === 0) {
                    // IMPORTANT Пишем ошибку, если нет деталей Раскроя
                    // __ Пишем в EventLog Ошибку
                    $eventLog          = new EventLog();
                    $eventLog->level   = EventLog::LEVEL_WARNING;
                    $eventLog->target  = EventLog::TARGET_CUTTING_TASK;
                    $eventLog->message = 'Не найдены детали раскроя';
                    $eventLog->context = [
                        'spec_code_1c' => $line->construct_code_1c,
                        'model_name'   => $line->model_name,
                        'order'        => $order->order_no_origin,
                    ];
                    $eventLog->save();

                    $hasError         = true;
                    $constructDetails = $defaultDetails;
                } else {
                    // __ Нет деталей Крышки
                    if (!isset($constructDetails['panels'])) {
                        // IMPORTANT Пишем ошибку, если нет деталей Крышки
                        // __ Пишем в EventLog Ошибку
                        $eventLog          = new EventLog();
                        $eventLog->level   = EventLog::LEVEL_WARNING;
                        $eventLog->target  = EventLog::TARGET_CUTTING_TASK;
                        $eventLog->message = 'Не найдены Крышки';
                        $eventLog->context = [
                            'spec_code_1c' => $line->construct_code_1c,
                            'model_name'   => $line->model_name,
                            'order'        => $order->order_no_origin,
                        ];
                        $eventLog->save();
                    }

                    // __ Неверное Количество Крышек
                    if (isset($constructDetails['panels'])) {
                        if (!(($constructDetails['panels'][0]['type'] === ModelConstruct::PANEL_NAME && $constructDetails['panels'][0]['amount'] === 2) ||
                            (count($constructDetails['panels']) === 2))) {
                            // IMPORTANT Пишем ошибку, если Неверное Количество Крышек
                            // __ Пишем в EventLog Ошибку
                            $eventLog          = new EventLog();
                            $eventLog->level   = EventLog::LEVEL_WARNING;
                            $eventLog->target  = EventLog::TARGET_CUTTING_TASK;
                            $eventLog->message = 'Неверное количество Крышек';
                            $eventLog->context = [
                                'spec_code_1c' => $line->construct_code_1c,
                                'calc_code_1c' => $targetConstruct->code_1c,
                                'model_name'   => $line->model_name,
                                'order'        => $order->order_no_origin,
                            ];
                            $eventLog->save();
                        }
                    }

                    //$a = 0;

                    // __ Соединяем Расход и Детальки
                    if (isset($groupedPivotRecordsExpense[$line->id])) {
                        $expensePanels = [];
                        $expenseSides  = [];

                        foreach ($groupedPivotRecordsExpense[$line->id] as $recordExpense) {
                            if ($recordExpense->detail === ModelConstruct::DETAIL_CONSTRUCT_PANEL_NAME) {
                                $expensePanels[] = $recordExpense;
                            } elseif ($recordExpense->detail === ModelConstruct::DETAIL_CONSTRUCT_SIDE_NAME) {
                                $expenseSides[] = $recordExpense;
                            }
                        }

                        if (count($expensePanels) !== 2) {
                            // IMPORTANT Пишем ошибку, если Расход больше, чем на 2 Крышки
                            $a = 0;
                        }

                        // __ Объединяем Одинаковый расход
                        if (count($expensePanels) >= 2) {
                            if ($expensePanels[0]->material_code_1c === $expensePanels[1]->material_code_1c) {
                                $expensePanels[0]->expense += $expensePanels[1]->expense;
                                $expensePanels[0]->rest    += $expensePanels[1]->rest;
                                unset($expensePanels[1]);
                            }
                        }

                        // __ Тут на всякий случай смотрим на соответствие количества Крышек и Боковин
                        // __ которые пришли из определения количества Деталек Раскроя и Деталек из Расхода
                        // __ Должны совпадать
                        if (isset($constructDetails['panels'])) {
                            $minPanelCount = min(count($expensePanels), count($constructDetails['panels']));
                            for ($i = 0; $i < $minPanelCount; $i++) {
                                $constructDetails['panels'][$i]['total'] = $expensePanels[$i]->expense + $expensePanels[$i]->rest;
                            }
                        }

                        if (isset($constructDetails['sides'])) {
                            $minPanelCount = min(count($expenseSides), count($constructDetails['sides']));
                            for ($i = 0; $i < $minPanelCount; $i++) {
                                $constructDetails['sides'][$i]['total'] = $expenseSides[$i]->expense + $expenseSides[$i]->rest;
                            }
                        }
                    }
                }
                //$a = 0;
            }

            //if ($line->id === 27802) {
            //    $a = 0;
            //}


            // __ Получаем трудозатраты
            /** @noinspection DuplicatedCode */
            $cuttingTimeLabor = new CuttingTimeLabor(
                model: $line->model_code_1c,
                size: $line->size,
                amount: $line->amount,
            );

            // __ !!! Новая логика, когда в СЗ записываем отдельные трудозатраты в одно поле time для каждой ШМ + добавляем подмену свойств
            // __ Для каждой ШМ записываем отдельно
            $cuttingTables = [
                CuttingTaskLine::FIELD_TABLE_1   => [
                    'time'   => $cuttingTimeLabor->getTimeTable_1(),
                    'amount' => $cuttingTimeLabor->getAmountTable_1()
                ],
                CuttingTaskLine::FIELD_TABLE_2   => [
                    'time'   => $cuttingTimeLabor->getTimeTable_2(),
                    'amount' => $cuttingTimeLabor->getAmountTable_2()
                ],
                CuttingTaskLine::FIELD_TABLE_3   => [
                    'time'   => $cuttingTimeLabor->getTimeTable_3(),
                    'amount' => $cuttingTimeLabor->getAmountTable_3()
                ],
                CuttingTaskLine::FIELD_UNDEFINED => [
                    'time'   => $cuttingTimeLabor->getTimeUndefined(),
                    'amount' => $cuttingTimeLabor->getAmountUndefined()
                ],
            ];

            foreach ($cuttingTables as $table => $data) {
                if ($data['amount'] < 1) {
                    continue;
                }

                // __ Проверяем, что это базовая модель и у нее нет чехла, то пропускаем
                // __ Пока отключаем, потому что может прилететь 314, а там непонятно что
                //if (ModelsService::isElementBase($line->model_code_1c) && !ModelsService::hasElementCover($line->model_code_1c)) {
                //    continue;
                //}

                // !!! Новый код
                $workTable = $hasError ? CuttingTaskLine::FIELD_UNDEFINED : $table; // __ берем стол из объекта времени, он там определяеся
                foreach ($constructDetails['panels'] as $detail) {
                    $panelTaskLine = CuttingTaskLine::query()->create([
                        'cutting_task_id'  => $createdTask->id,
                        'order_line_id'    => $line->id,
                        'amount'           => $data['amount'] * $detail['amount'],
                        'expense'          => $detail['total'] ?? 0.0,
                        'position'         => $position++,
                        'time'             => $data['time'] * $detail['amount'],
                        'table'            => $workTable,

                        // __ Задаем детальки
                        'detail'           => $detail['type'],
                        'is_panel'         => true,
                        'has_panel'        => true,
                        'is_side'          => false,
                        'has_side'         => isset($constructDetails['sides']) && count($constructDetails['sides']) !== 0,
                        'fabric_construct' => [$detail['name']],
                        'cover_height'     => ModelsService::getModelCoverHeight($line->model_code_1c),

                        // __ Задаем подмену свойств
                        'phantom'          => $table,
                        'phantom_json'     => [$table => true],
                    ]);
                }

                // __ Пропускаем отсутствие Боковины
                if (!isset($constructDetails['sides'])) {
                    continue;
                }

                $workTable = CuttingTaskLine::FIELD_TABLE_3;    // __ Стол будет всегда Третий для деталек
                foreach ($constructDetails['sides'] as $detail) {
                    $panelTaskLine = CuttingTaskLine::query()->create([
                        'cutting_task_id'  => $createdTask->id,
                        'order_line_id'    => $line->id,
                        'amount'           => $data['amount'] * $detail['amount'],
                        'expense'          => $detail['total'] ?? 0.0,
                        'position'         => $position++,
                        'time'             => $data['time'] * $detail['amount'],
                        'table'            => $workTable,

                        // __ Задаем детальки
                        'detail'           => $detail['type'],
                        'is_panel'         => false,
                        'has_panel'        => false,
                        'is_side'          => true,
                        'has_side'         => false,
                        'fabric_construct' => [$detail['name']],
                        'cover_height'     => ModelsService::getModelCoverHeight($line->model_code_1c),

                        // __ Задаем подмену свойств
                        'phantom'          => $table,
                        'phantom_json'     => [$table => true],
                    ]);
                }


                // !!! __ Старый код

                //// __ Получаем стол + С тканью из Спецификаций
                ////$targetTable = CuttingService::getTableByModel($line->model_code_1c);
                //$panelFabrics = [];
                //$hasPanel     = CuttingService::hasPanel($line->model_code_1c, $panelFabrics);
                //
                //$sideFabrics = [];
                //$hasSide     = CuttingService::hasSide($line->model_code_1c, $sideFabrics);
                //
                //$workTable = $table; // __ берем стол из объекта времени, он там определяеся
                //
                //
                ////$constructDetails
                //
                //// __ Обрабатываем ситуацию, когда у чехла есть и Крышка и Боковина, но система выдает 3 Стол
                //// __ Отправляем Крышки на 2 Стол, а Боковины на 3 Стол
                //if ($workTable === CuttingTaskLine::FIELD_TABLE_3 && $hasSide && $hasPanel) {
                //    $workTable = CuttingTaskLine::FIELD_TABLE_2;
                //}
                //
                //$panelTaskLine = CuttingTaskLine::query()->create([
                //    'cutting_task_id'  => $createdTask->id,
                //    'order_line_id'    => $line->id,
                //    'amount'           => $data['amount'],
                //    'position'         => $position++,
                //    'time'             => $data['time'],
                //    'table'            => $workTable,
                //    //'table'           => $targetTable,
                //
                //    // __ Задаем детальки
                //    'is_panel'         => true,
                //    'has_panel'        => $hasPanel,
                //    'is_side'          => false,
                //    'has_side'         => $hasSide,
                //    'fabric_construct' => $panelFabrics,
                //    'cover_height'     => ModelsService::getModelCoverHeight($line->model_code_1c),
                //
                //    // __ Задаем подмену свойств
                //    'phantom'          => $table,
                //    'phantom_json'     => [$table => true],
                //]);
                //
                ////if ($table === CuttingTaskLine::FIELD_TABLE_3) {
                ////    $lineArray = $line->toArray();
                ////    $panelTaskLineArray = $panelTaskLine->toArray();
                ////    $a = 0;
                ////}
                //
                //// __ Если нет Боковины у Изделия, то пропускаем
                //if (!$hasSide) {
                //    continue;
                //}
                //
                //try {
                //    // __ Создаем Боковину
                //    $baseTaskLine = CuttingTaskLine::query()->create([
                //        'cutting_task_id'  => $createdTask->id,
                //        'order_line_id'    => $line->id,
                //        'parent_id'        => $panelTaskLine->id,
                //        // __ Указываем родительскую панельку
                //        'amount'           => $data['amount'],
                //        'position'         => $position++,
                //        'time'             => $data['time'],
                //        'table'            => $table === CuttingTaskLine::FIELD_UNDEFINED ? CuttingTaskLine::FIELD_UNDEFINED : CuttingTaskLine::FIELD_TABLE_3,
                //        //'table'           => $targetTable === CuttingTaskLine::FIELD_UNDEFINED ? CuttingTaskLine::FIELD_UNDEFINED : CuttingTaskLine::FIELD_TABLE_3,
                //        // __ Все детальки отправляем на Стол 3
                //
                //        // __ Задаем детальки
                //        'is_panel'         => false,
                //        'has_panel'        => false,
                //        'is_side'          => true,
                //        'has_side'         => false,
                //        'fabric_construct' => $sideFabrics,
                //        'cover_height'     => ModelsService::getModelCoverHeight($line->model_code_1c),
                //
                //        // __ Задаем подмену свойств
                //        'phantom'          => $table,
                //        'phantom_json'     => [$table => true],
                //    ]);
                //
                //    //$b = $baseTaskLine->toArray();
                //    //$c = 0;

                //} catch (Exception $e) {
                //    $err = $e->getMessage();
                //    continue;
                //}
            }
        }

        // __ Создаем запись в Статусе: Создано
        $createdTask->statuses()->attach([
            CuttingTaskStatus::CUTTING_STATUS_CREATED_ID => [
                'set_at'     => now(),
                'created_by' => auth()->id(),
            ]
        ]);

        // });
        return $createdTask;
        //} catch (Exception|Throwable $e) {
        //    $a = 0;
        //    return EndPointStaticRequestAnswer::fail($e);
        //}
    }


    /**
     *  ___ Распределяем СЗ по Частям СЗ
     *  ___ Сюда приходим тогда, когда есть прогнозное СЗ, а поверх него загружаем Заявку
     * @param int $orderId
     * @return bool
     * @throws Throwable
     */
    public static function distributeCuttingTaskFromOrderId(int $orderId): bool
    {
        // !!! Костыль !!! Доработать процедуру распределения
        self::createCuttingTaskFromOrderId($orderId);
        return true;

        // __ Получаем саму Заявку
        $order = Order::query()
            ->with(['lines', 'client'])
            ->find($orderId);

        if (!$order) {
            return false;
        }

        // __ Получаем СЗ. Оно тут должно быть
        $cuttingTasks = CuttingTask::query()
            ->where('order_id', $orderId)
            ->with('cuttingLines')
            ->orderBy('action_at')      // __ Сортируем по дате пр-ва сначала
            ->orderBy('position')       // __ Потом по позиции в СЗ
            ->get();

        if (!$cuttingTasks) {
            return false;
        } else {
            // __ Создаем СЗ из Заявки, если по какой-то причине оно отсутствует
            // $cuttingTask = self::createCuttingTaskFromOrderId($orderId); // __ Пока оставляем так
        }

        // ___ Первый проход. Распределяем содержимое Заявки по Частям СЗ

        // __ Разбиваем Заявку на ШМ
        $universals = [];
        $autos      = [];
        $solidHards = [];
        $solidLites = [];
        $unknowns   = [];

        foreach ($order->lines as $orderLine) {
            $model = ModelsService::getModelByCode1C($orderLine->model_code_1c);

            if (!ModelsService::isElementAverage($model)) {
                match ($model->machine_type_name) {
                    CuttingTask::FIELD_UNIVERSAL  => $universals[] = $orderLine,
                    CuttingTask::FIELD_AUTO       => $autos[] = $orderLine,
                    CuttingTask::FIELD_SOLID_HARD => $solidHards[] = $orderLine,
                    CuttingTask::FIELD_SOLID_LITE => $solidLites[] = $orderLine,
                    default                       => $unknowns[] = $orderLine,
                };
            }
        }

        $FIELD_DATA   = 'data';
        $FIELD_MEMORY = 'memory';
        $FIELD_MERGED = 'merged';

        $cuttingTaskContent = [];
        // $idx               = 0;
        foreach ($cuttingTasks as $key => $cuttingTask) {
            foreach ($cuttingTask->cuttingLines as $cuttingLine) {
                switch ($cuttingLine->phantom):
                    case CuttingTask::FIELD_UNIVERSAL:
                        $workField = CuttingTask::FIELD_UNIVERSAL;
                        $workArray = &$universals;
                        break;
                    case CuttingTask::FIELD_AUTO:
                        $workField = CuttingTask::FIELD_AUTO;
                        $workArray = &$autos;
                        break;
                    case CuttingTask::FIELD_SOLID_HARD:
                        $workField = CuttingTask::FIELD_SOLID_HARD;
                        $workArray = &$solidHards;
                        break;
                    case CuttingTask::FIELD_SOLID_LITE:
                        $workField = CuttingTask::FIELD_SOLID_LITE;
                        $workArray = &$solidLites;
                        break;
                    default:
                        $workField = CuttingTask::FIELD_UNDEFINED;
                        $workArray = &$unknowns;
                endswitch;

                // __ Запоминаем расчетную информацию для данной ШМ
                if (!isset($cuttingTaskContent[$key][$workField][$FIELD_MEMORY])) {
                    $cuttingTaskContent[$key][$workField][$FIELD_MEMORY] = $cuttingLine->amount;
                }

                $lineAmount = $cuttingTaskContent[$key][$workField][$FIELD_MEMORY];

                $data = [];

                // __ Если есть что распределять и есть куда распределять
                while ($lineAmount > 0 && count($workArray) > 0) {
                    $item = array_pop($workArray);

                    if ($item->amount <= $lineAmount) {
                        // __ Если есть куда распределить
                        $data[]     = $item;
                        $lineAmount -= $item->amount;
                    } else {
                        // __ Если не хватает для распределения
                        $newItem         = clone $item; // Создаем копию, чтобы изменения не затронули оригинал
                        $newItem->amount = $lineAmount;

                        $data[] = $newItem;

                        $item->amount -= $lineAmount;     // Уменьшаем остаток в оригинале
                        $workArray[]  = $item;            // Возвращаем остаток в массив

                        $lineAmount = 0;
                    }
                }

                // __ Записываем распределение
                $cuttingTaskContent[$key][$workField][$FIELD_DATA] = $data;

                // __ Записывает текущий остаток для следующего распределения
                $cuttingTaskContent[$key][$workField][$FIELD_MEMORY] = $lineAmount;
            }
        }

        // ___ Второй проход. Распределяем все, что осталось

        // __ Складываем все в одну кучу
        $rest = array_merge($universals, $autos, $solidHards, $solidLites, $unknowns);

        if (count($rest) > 0) {
            foreach ($cuttingTasks as $key => $cuttingTask) {
                foreach ($cuttingTask->cuttingLines as $cuttingLine) {
                    $workField = match ($cuttingLine->phantom) {
                        CuttingTask::FIELD_UNIVERSAL  => CuttingTask::FIELD_UNIVERSAL,
                        CuttingTask::FIELD_AUTO       => CuttingTask::FIELD_AUTO,
                        CuttingTask::FIELD_SOLID_HARD => CuttingTask::FIELD_SOLID_HARD,
                        CuttingTask::FIELD_SOLID_LITE => CuttingTask::FIELD_SOLID_LITE,
                        default                       => CuttingTask::FIELD_UNDEFINED,
                    };

                    $lineAmount = $cuttingTaskContent[$key][$workField][$FIELD_MEMORY];

                    // __ Если есть что распределять и есть куда распределять
                    while ($lineAmount > 0 && count($rest) > 0) {
                        $item = array_pop($rest);

                        if ($item->amount <= $lineAmount) {
                            // __ Если есть куда распределить
                            $cuttingTaskContent[$key][$workField][$FIELD_DATA][] = $item;

                            $lineAmount -= $item->amount;
                        } else {
                            // __ Если не хватает для распределения
                            $newItem         = clone $item; // Создаем копию, чтобы изменения не затронули оригинал
                            $newItem->amount = $lineAmount;

                            $cuttingTaskContent[$key][$workField][$FIELD_DATA][] = $newItem;

                            $item->amount -= $lineAmount;             // Уменьшаем остаток в оригинале
                            $rest[]       = $item;                    // Возвращаем остаток в массив []

                            $lineAmount = 0;
                        }
                    }

                    $cuttingTaskContent[$key][$workField][$FIELD_MEMORY] = $lineAmount;
                }
            }
        }

        // ___ Третья попытка, когда реальная заявка больше плановой и есть остаток

        // __ Сгружаем в первую часть
        if (count($rest) > 0) {
            $cuttingTaskContent[0][CuttingTask::FIELD_UNIVERSAL][$FIELD_DATA] =
                array_merge($cuttingTaskContent[0][CuttingTask::FIELD_UNIVERSAL][$FIELD_DATA], $rest);
        }

        // __ Теперь нужно объединить все те Lines, которые были разбиты на несколько и попали в одну часть СЗ
        foreach ($cuttingTasks as $key => $cuttingTask) {
            $merged = [];

            foreach ($cuttingTask->cuttingLines as $cuttingLine) {
                $workField = match ($cuttingLine->phantom) {
                    CuttingTask::FIELD_UNIVERSAL  => CuttingTask::FIELD_UNIVERSAL,
                    CuttingTask::FIELD_AUTO       => CuttingTask::FIELD_AUTO,
                    CuttingTask::FIELD_SOLID_HARD => CuttingTask::FIELD_SOLID_HARD,
                    CuttingTask::FIELD_SOLID_LITE => CuttingTask::FIELD_SOLID_LITE,
                    default                       => CuttingTask::FIELD_UNDEFINED,
                };

                // __ Сгружаем в кучу
                $merged = array_merge($merged, $cuttingTaskContent[$key][$workField][$FIELD_DATA]);
            }

            $result = [];

            foreach ($merged as $line) {
                $code = $line->id;

                if (!isset($result[$code])) {
                    // __ Если такой код встретили впервые — клонируем объект,
                    // __ чтобы не испортить оригинал в исходном массиве
                    $result[$code] = clone $line;
                } else {
                    // __ Если код уже есть — просто прибавляем количество
                    $result[$code]->amount += $line->amount;
                }
            }

            // __ Сбрасываем ключи, чтобы вернуть обычный индексированный массив [0, 1, 2...]
            $cuttingTaskContent[$key][$FIELD_MERGED] = array_values($result);
        }

        // __ Сохраняем содержимое
        DB::transaction(function () use ($cuttingTasks, $cuttingTaskContent, $FIELD_MERGED) {
            // __ Заводим позиции записей в СЗ в отрицательную зону
            // __ Или удаляем
            foreach ($cuttingTasks as $key => $cuttingTask) {
                foreach ($cuttingTask->cuttingLines as $cuttingLine) {
                    $cuttingLine->delete();
                }
            }

            // __ Записываем содержимое в Части СЗ в БД
            foreach ($cuttingTasks as $key => $cuttingTask) {
                if (isset($cuttingTaskContent[$key])) {
                    $position = 1;

                    if (isset($cuttingTaskContent[$key][$FIELD_MERGED])) {
                        foreach ($cuttingTaskContent[$key][$FIELD_MERGED] as $line) {
                            // __ Получаем трудозатраты
                            /** @noinspection DuplicatedCode */
                            $cuttingTimeLabor = new CuttingTimeLabor(
                                model: $line->model_code_1c,
                                size: $line->size,
                                amount: $line->amount,
                            );

                            $createdTaskLine = CuttingTaskLine::query()->create([
                                'cutting_task_id' => $cuttingTask->id,
                                'order_line_id'   => $line->id,
                                'amount'          => $line->amount,
                                'position'        => $position++,
                                'time'            => $cuttingTimeLabor->getTime(),
                            ]);
                        }
                    }
                } else {
                    $cuttingTask->delete();
                }
            }
        });

        return true;
    }


    /**
     * ___ Получаем позицию последнего СЗ в дне
     * @param string|Carbon|null $date Дата нужного дня
     * @return int
     */
    public static function getCuttingTaskLastPositionInDay(string|Carbon $date = null): int
    {
        if (is_null($date) || $date === '') {
            return 0;
        }

        $date = normalizeToCarbon($date);

        $pos = CuttingTask::query()->whereDate('action_at', $date)->max('position');
        if (!is_null($pos)) {
            return $pos;
        }
        return CuttingTask::query()
            ->whereDate('action_at', $date)
            ->count();
    }


    /**
     * __ Возвращаем следующую производственную смену
     * @param ManufactureDayAndChange|Carbon|string $manufactureEntity
     * @param int|null $change
     * @return ManufactureDayAndChange
     */
    public static function getNextChange(
        ManufactureDayAndChange|Carbon|string $manufactureEntity,
        int $change = null
    ): ManufactureDayAndChange {
        $manufDateAndChange = null;
        if ($manufactureEntity instanceof ManufactureDayAndChange) {
            $manufDateAndChange = new ManufactureDayAndChange($manufactureEntity->getManufactureDay()->addDay(), 1);
        } else {
            $manufDateAndChange = new ManufactureDayAndChange(normalizeToCarbon($manufactureEntity)->addDay(), 1);
            // $manufDateAndChange = new ManufactureDayAndChange(normalizeToCarbon($manufactureEntity)->addDay(), $change);
        }

        return $manufDateAndChange;
    }


    /**
     * ___ Массовое обновление СЗ
     * @param array $rows
     * @return void
     * @throws Throwable
     */
    public static function bulkUpdateTasks(array $rows): void
    {
        // ___ Формат входных данных:
        // $tasksToUpdate[] = [
        //     'id'        => taskId,
        //     'action_at' => new_action_at ?? null,
        //     'position'  => new_position ?? null,
        // ];


        // __ Получаем имя таблицы
        $table = (new CuttingTask)->getTable();

        // __ 1. Находим только те ID, у которых действительно меняется позиция (чтобы не уводить в минус лишнее)
        $idsForMinus = array_column(array_filter($rows, fn($r) => isset($r['position'])), 'id');

        // __ 2. Находим все ID, которые участвуют в обновлении (хоть позиция, хоть amount)
        $allIds = array_column($rows, 'id');

        DB::transaction(function () use ($table, $rows, $idsForMinus, $allIds) {
            // __ ШАГ 1: Уводим в минус ТОЛЬКО те записи, где позиция реально будет обновлена
            if (!empty($idsForMinus)) {
                $placeholders = implode(',', array_fill(0, count($idsForMinus), '?'));
                DB::update("UPDATE {$table} SET position = (id * -1) WHERE id IN ({$placeholders})", $idsForMinus);
            }

            // __ ШАГ 2: Собираем финальный запрос
            $casesActionAt  = [];
            $paramsActionAt = [];
            $casesPosition  = [];
            $paramsPosition = [];

            foreach ($rows as $row) {
                if (isset($row['action_at'])) {
                    $casesActionAt[] = "WHEN id = ? THEN ?";
                    array_push($paramsActionAt, $row['id'], $row['action_at']);
                }
                if (isset($row['position'])) {
                    $casesPosition[] = "WHEN id = ? THEN ?";
                    array_push($paramsPosition, $row['id'], $row['position']);
                }
            }

            $setParts    = [];
            $finalParams = [];

            if (!empty($casesActionAt)) {
                $setParts[]  = "action_at = CASE " . implode(' ', $casesActionAt) . " ELSE action_at END";
                $finalParams = array_merge($finalParams, $paramsActionAt);
            }

            if (!empty($casesPosition)) {
                $setParts[]  = "position = CASE " . implode(' ', $casesPosition) . " ELSE position END";
                $finalParams = array_merge($finalParams, $paramsPosition);
            }

            if (empty($setParts)) {
                return;
            }

            $wherePlaceholders = implode(',', array_fill(0, count($allIds), '?'));
            $sql               = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE id IN ({$wherePlaceholders})";

            // __ Соединяем параметры: параметры CASE1 + параметры CASE2 + параметры WHERE
            DB::update($sql, array_merge($finalParams, $allIds));
        });
    }


    /**
     * ___ Получаем стол по модели
     * @param Model|string $model
     * @return string
     */
    public static function getTableByModel(Model|string $model): string
    {
        $findModel = ModelsService::getModel($model);
        if (!$findModel) {
            return CuttingTaskLine::FIELD_UNDEFINED;
        }

        /*
            __ Для сортировки по столам получаются вот такие условия.
            __ Тип чехла - источник карточка Модели товара, вкладка КД
            __ Деталь - источник Спецификация на модель, столбец Деталь
            __ *Если Тип чехла=м.авт и Деталь=Крышка тогда Стол 1.
            __ *Если Тип чехла=м.бок. или Тип чехла=глух. и Деталь=Крышка тогда Стол 2.
            __ *Если Тип чехла=м.бок. или Тип чехла=глух. и Деталь=Боковина тогда Стол 3.
            __ *Если Тип чехла=м.книж. и Деталь=Крышка тогда Стол 3.
        */

        if ($findModel->cover_type === 'м.авт') {
            return CuttingTaskLine::FIELD_TABLE_1;
        }

        // __ Используем такую конструкцию, потому что в модели есть поле cover
        $cover = $findModel->getRelation('cover');

        if (!is_null($cover) && !is_null($cover->constructs)) {
            // __ Та ситуация, когда у 1 чехла несколько спецификаций
            // __ Пока пропускаем
            //if (count($cover_type->constructs) > 1) {
            //    return CuttingTaskLine::FIELD_UNDEFINED;
            //}

            //$coverConstruct = $cover->constructs[0]->toArray();
            //$coverConstructItems = $cover->constructs[0]->constructItems;
            //$cond_1 = !is_null($cover->constructs[0]);
            //$cond_2 = !is_null($cover->constructs[0]->constructItems);

            // __ Ищем крышку и боковину
            $hasPanel = false;
            $hasSide  = false;
            if (!is_null($cover->constructs[0]) && !is_null($cover->constructs[0]->constructItems)) {
                $items = $cover->constructs[0]->constructItems;
                foreach ($items as $item) {
                    if (!is_null($item->detail) && mb_strtolower($item->detail) === mb_strtolower(ModelConstruct::DETAIL_CONSTRUCT_PANEL_NAME)) {
                        $hasPanel = true;
                    } elseif (!is_null($item->detail) && mb_strtolower($item->detail) === mb_strtolower(ModelConstruct::DETAIL_CONSTRUCT_SIDE_NAME)) {
                        $hasSide = true;
                    }
                }
            }

            if ($findModel->cover_type === 'м.бок.' || $findModel->cover_type === 'глух.') {
                if ($hasPanel) {
                    return CuttingTaskLine::FIELD_TABLE_2;
                }

                //if ($hasPanel && !$hasSide) {
                //    return CuttingTaskLine::FIELD_TABLE_2;
                //} elseif ($hasSide) {
                //    return CuttingTaskLine::FIELD_TABLE_3;
                //}
            } elseif ($findModel->cover_type === 'м.книж.') {
                if ($hasPanel) {
                    return CuttingTaskLine::FIELD_TABLE_3;
                }
            }
        }

        return CuttingTaskLine::FIELD_UNDEFINED;
    }


    /**
     * ___ Проверяем, есть ли Крышка у Модели в Спецификации
     * @param Model|string $model
     * @param array $fabricList
     * @return bool
     */
    public static function hasPanel(Model|string $model, array &$fabricList = []): bool
    {
        $fabricList = [];

        $findModel = ModelsService::getModel($model);
        if (!$findModel) {
            return false;
        }

        // __ Используем такую конструкцию, потому что в модели есть поле cover
        $cover = $findModel->getRelation('cover');

        if (!is_null($cover) && !is_null($cover->constructs)) {
            // __ Ищем крышку и боковину
            if (!is_null($cover->constructs[0]) && !is_null($cover->constructs[0]->constructItems)) {
                $items = $cover->constructs[0]->constructItems;
                foreach ($items as $item) {
                    if (!is_null($item->detail) && mb_strtolower($item->detail) === mb_strtolower(ModelConstruct::DETAIL_CONSTRUCT_PANEL_NAME)) {
                        $fabricList[] = $item->material_name;
                    }
                }

                return count($fabricList) !== 0;
            }
        }

        return false;
    }

    /**
     * ___ Проверяем, есть ли Боковина у Модели в Спецификации
     * @param Model|string $model
     * @param array $fabricList
     * @return bool
     */
    public static function hasSide(Model|string $model, array &$fabricList = []): bool
    {
        $fabricList = [];
        $findModel  = ModelsService::getModel($model);
        if (!$findModel) {
            return false;
        }

        // __ Используем такую конструкцию, потому что в модели есть поле cover
        $cover = $findModel->getRelation('cover');

        if (!is_null($cover) && !is_null($cover->constructs)) {
            // __ Ищем крышку и боковину
            if (!is_null($cover->constructs[0]) && !is_null($cover->constructs[0]->constructItems)) {
                $items = $cover->constructs[0]->constructItems;
                foreach ($items as $item) {
                    if (!is_null($item->detail) && mb_strtolower($item->detail) === mb_strtolower(ModelConstruct::DETAIL_CONSTRUCT_SIDE_NAME)) {
                        $fabricList[] = $item->material_name;
                    }
                }

                return count($fabricList) !== 0;
            }
        }

        return false;
    }


    /**
     * ___ Проверяет, есть ли в Спецификации Чехол
     * @param ModelConstruct|array|null $construct
     * @return bool
     * @noinspection PhpUndefinedFieldInspection, DuplicatedCode
     */
    public static function hasModelConstructCover(ModelConstruct|array|null $construct): bool
    {
        if (is_null($construct)) {
            return false;
        }

        $constructItems = [];
        if ($construct instanceof ModelConstruct) {
            $constructItems = $construct->constructItems;
        } elseif (is_array($construct)) {
            $constructItems = $construct;
        } else {
            return false;
        }

        foreach ($constructItems as $item) {
            if (mb_strripos(mb_strtolower($item->procedure_name), mb_strtolower('ПодборЧехлаДляМатраса')) !== false ||
                mb_strripos(mb_strtolower($item->procedure_name), mb_strtolower('ПодборЧехлаДляНаматрасника')) !== false) {
                return true;
            }
        }

        return false;
    }


    /**
     * ___ Возвращает Спецификацию Чехла по Спецификации МЭ
     * @param ModelConstruct|array|null $construct
     * @return ModelConstruct|null
     * @noinspection PhpUndefinedFieldInspection, DuplicatedCode
     */
    public static function getModelConstructCover(ModelConstruct|array|null $construct): ?ModelConstruct
    {
        if (is_null($construct)) {
            return null;
        }

        $constructItems = [];
        if ($construct instanceof ModelConstruct) {
            $constructItems = $construct->constructItems;
        } elseif (is_array($construct)) {
            $constructItems = $construct;
        } else {
            return null;
        }

        foreach ($constructItems as $item) {
            if (mb_stripos(mb_strtolower($item->procedure_name), mb_strtolower('ПодборЧехлаДляМатраса')) !== false ||
                mb_stripos(mb_strtolower($item->procedure_name), mb_strtolower('ПодборЧехлаДляНаматрасника')) !== false) {
                if (isset($item->semiproductConstruct)) {
                    return $item->semiproductConstruct;
                }
            }
        }

        return null;
    }


    /**
     * ___ Получаем Расклад Раскроя по Спецификации
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function getCuttingDetailsByConstruct(ModelConstruct|Collection|null $construct): array
    {
        $panels = [];
        $sides  = [];
        $result = [];

        if (is_null($construct)) {
            return $result;
        }

        // __ Ищем крышку и боковину
        $hasPanel = false;
        $hasSide  = false;
        $cover    = ModelConstruct::PANEL_UP_NAME;

        $constructDebug = $construct->toArray();

        if ($construct instanceof ModelConstruct) {
            $items = $construct->constructItems;
        } else {
            $items = $construct;
        }

        //$items = $items->sortBy('position');  // __ Сортируем при выборке

        if (!is_null($items)) {
            foreach ($items as $item) {
                if (!is_null($item->detail) && mb_strtolower($item->detail) === mb_strtolower(ModelConstruct::DETAIL_CONSTRUCT_PANEL_NAME)) {
                    $hasPanel = true;
                    $panels[] = [
                        'name'     => $item->material_name,
                        'code'     => $item->material_code_1c,
                        'item'     => $item,
                        'type'     => $cover,
                        'position' => $item->position,
                        'amount'   => 1,
                    ];
                    $cover    = ModelConstruct::PANEL_DOWN_NAME;
                } elseif (!is_null($item->detail) && mb_strtolower($item->detail) === mb_strtolower(ModelConstruct::DETAIL_CONSTRUCT_SIDE_NAME)) {
                    $hasSide = true;
                    $sides[] = [
                        'name'     => $item->material_name,
                        'code'     => $item->material_code_1c,
                        'item'     => $item,
                        'type'     => ModelConstruct::SIDE_NAME,
                        'position' => $item->position,
                        'amount'   => 1,
                    ];
                }
            }
        }

        //if ($construct->code_1c === "БП0002321") {
        //    $a = 0;
        //}

        if (count($panels) > 1) {
            // __ Если Крышек больше 2, то пишем ошибку
            if (count($panels) > 2) {
                // __ Пишем в EventLog Ошибку
                $eventLog          = new EventLog();
                $eventLog->level   = EventLog::LEVEL_WARNING;
                $eventLog->target  = EventLog::TARGET_CUTTING_TASK;
                $eventLog->message = 'Больше двух Крышек в Спецификации';
                $eventLog->context = [
                    'code_1c' => $construct->code_1c,
                    'name'    => $construct->name,
                ];
                $eventLog->save();
            } else {
                // __ Склеиваем одинаковые Крышки
                if ($panels[0]['item']->material_code_1c_copy === $panels[1]['item']->material_code_1c_copy &&
                    $panels[0]['item']->procedure_code_1c_copy === $panels[1]['item']->procedure_code_1c_copy &&
                    $panels[0]['item']->amount === $panels[1]['item']->amount) {
                    $panels              = [$panels[0]];
                    $panels[0]['type']   = ModelConstruct::PANEL_NAME; // __ Пишем общее название
                    $panels[0]['amount'] = 2;
                }
            }
        }

        unset($panels[0]['item'], $panels[1]['item'], $panels[2]['item'], $sides[0]['item'], $sides[1]['item'], $sides[2]['item']);

        if ($hasPanel) {
            usort($panels, fn($a, $b) => $a['position'] <=> $b['position']);
            $result['panels'] = $panels;
        }
        if ($hasSide) {
            usort($sides, fn($a, $b) => $a['position'] <=> $b['position']);
            $result['sides'] = $sides;
        }

        return $result;
    }


    public static function test()
    {
        $cuttingTask = CuttingService::createCuttingTaskFromOrderId(1163);
        $cuttingTask = CuttingService::createCuttingTaskFromOrderId(1172);

        //$model   = ModelsService::getModel('000000137');
        //$hasSide = self::hasSide($model);
        //$table   = self::getTableByModel($model);
        //
        ////$model = ModelsService::getModel('000002853');
        //$modelArray = $model->toArray();


        $a = 0;
    }


}
