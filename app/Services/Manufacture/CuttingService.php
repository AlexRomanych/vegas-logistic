<?php

namespace App\Services\Manufacture;


use App\Classes\EndPointStaticRequestAnswer;
use App\Classes\ManufactureDayAndChange;
use App\Classes\CuttingTimeLabor;
use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskLine;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskStatus;
use App\Models\Order\Order;
use App\Services\BusinessProcessesService;
use App\Services\ModelsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CuttingService
{

    /**
     * ___ Создать СЗ для Пошива из основного Заказа
     * @param int $orderId ID основного Заказа
     * @param string|null $plannedDate Дата планируемого выполнения СЗ - должна быть либо дата, либо смещение, приоритет - дата
     * @return CuttingTask|null
     * @throws Throwable
     */
    public static function createCuttingTaskFromOrderId(
        int         $orderId,
        string|null $plannedDate = null
    ): ?CuttingTask
    {
        // try {
        // __ Проверяем на существование заказа
        $order = Order::query()->with(['lines', 'client'])->find($orderId);
        if (!$order) {
            return null;
        }

        if (!(is_null($plannedDate) || $plannedDate === '')) {
            $plannedDate = normalizeToCarbon($plannedDate);
        } else {
            // __ Получаем смещение в днях для Пошива
            $offset = BusinessProcessesService::getDateOffsetForOrderMovingProcessByNodeIdAndClientId(CUTTING_NODE_ID, $order->client->id);
            $plannedDate = normalizeToCarbon($order->load_at)->addDays($offset);
        }

        $createdTask = null;
        // DB::transaction(function () use ($order, $plannedDate, &$createdTask) {

        // __ Создаем СЗ
        $createdTask = CuttingTask::query()->create([
            'action_at' => $plannedDate,
            'order_id'  => $order->id,
            'position'  => self::getCuttingTaskLastPositionInDay($plannedDate) + 1, // __ Получаем позицию для новой СЗ
        ]);
        if (!$createdTask) {
            throw new Exception('Failed to create CuttingTask');
        }

        // __ Создаем контент (строки) СЗ
        $position = 1;
        foreach ($order->lines as $line) {
            // __ Если это расчетная модель (AVERAGE), то ставим позицию 0
            // if ($order->lines->count() === 1 && ModelsService::isElementAverage($line->model_code_1c)) {
            //     $position = 0;
            // }

            // __ Получаем трудозатраты
            /** @noinspection DuplicatedCode */
            $cuttingTimeLabor = new CuttingTimeLabor(
                model: $line->model_code_1c,
                size: $line->size,
                amount: $line->amount,
            );


            // __ !!! Новая логика, когда в СЗ записываем отдельные трудозатраты в одно поле time для каждой ШМ + добавляем подмену свойств
            // __ Для каждой ШМ записываем отдельно
            $cuttingMachines = [
                CuttingTask::FIELD_UNIVERSAL  => [
                    'time'   => $cuttingTimeLabor->getTimeUniversal(),
                    'amount' => $cuttingTimeLabor->getAmountUniversal()
                ],
                CuttingTask::FIELD_AUTO       => [
                    'time'   => $cuttingTimeLabor->getTimeAuto(),
                    'amount' => $cuttingTimeLabor->getAmountAuto()
                ],
                CuttingTask::FIELD_SOLID_HARD => [
                    'time'   => $cuttingTimeLabor->getTimeSolidHard(),
                    'amount' => $cuttingTimeLabor->getAmountSolidHard()
                ],
                CuttingTask::FIELD_SOLID_LITE => [
                    'time'   => $cuttingTimeLabor->getTimeSolidLite(),
                    'amount' => $cuttingTimeLabor->getAmountSolidLite()
                ],
                CuttingTask::FIELD_UNDEFINED  => [
                    'time'   => $cuttingTimeLabor->getTimeUndefined(),
                    'amount' => $cuttingTimeLabor->getAmountUndefined()
                ],
            ];

            foreach ($cuttingMachines as $machine => $data) {
                if ($data['amount'] < 1) {
                    continue;
                }
                $createdTaskLine = CuttingTaskLine::query()->create([
                    'cutting_task_id' => $createdTask->id,
                    'order_line_id'  => $line->id,
                    'amount'         => $data['amount'],
                    'position'       => $position++,
                    'time'           => $data['time'],

                    // __ Задаем подмену свойств
                    'phantom'        => $machine,
                    'phantom_json'   => ['is_' . $machine => true],

                ]);
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
        // } catch (Exception|Throwable $e) {
        //     return EndPointStaticRequestAnswer::fail($e);
        // }


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
        $autos = [];
        $solidHards = [];
        $solidLites = [];
        $unknowns = [];

        foreach ($order->lines as $orderLine) {
            $model = ModelsService::getModelByCode1C($orderLine->model_code_1c);

            if (!ModelsService::isElementAverage($model)) {
                match ($model->machine_type_name) {
                    CuttingTask::FIELD_UNIVERSAL => $universals[] = $orderLine,
                    CuttingTask::FIELD_AUTO => $autos[] = $orderLine,
                    CuttingTask::FIELD_SOLID_HARD => $solidHards[] = $orderLine,
                    CuttingTask::FIELD_SOLID_LITE => $solidLites[] = $orderLine,
                    default => $unknowns[] = $orderLine,
                };

            }
        }

        $FIELD_DATA = 'data';
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
                        $data[] = $item;
                        $lineAmount -= $item->amount;
                    } else {
                        // __ Если не хватает для распределения
                        $newItem = clone $item; // Создаем копию, чтобы изменения не затронули оригинал
                        $newItem->amount = $lineAmount;

                        $data[] = $newItem;

                        $item->amount -= $lineAmount;    // Уменьшаем остаток в оригинале
                        $workArray[] = $item;           // Возвращаем остаток в массив

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
                        CuttingTask::FIELD_UNIVERSAL => CuttingTask::FIELD_UNIVERSAL,
                        CuttingTask::FIELD_AUTO => CuttingTask::FIELD_AUTO,
                        CuttingTask::FIELD_SOLID_HARD => CuttingTask::FIELD_SOLID_HARD,
                        CuttingTask::FIELD_SOLID_LITE => CuttingTask::FIELD_SOLID_LITE,
                        default => CuttingTask::FIELD_UNDEFINED,
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
                            $newItem = clone $item; // Создаем копию, чтобы изменения не затронули оригинал
                            $newItem->amount = $lineAmount;

                            $cuttingTaskContent[$key][$workField][$FIELD_DATA][] = $newItem;

                            $item->amount -= $lineAmount;       // Уменьшаем остаток в оригинале
                            $rest[] = $item;              // Возвращаем остаток в массив []

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
                    CuttingTask::FIELD_UNIVERSAL => CuttingTask::FIELD_UNIVERSAL,
                    CuttingTask::FIELD_AUTO => CuttingTask::FIELD_AUTO,
                    CuttingTask::FIELD_SOLID_HARD => CuttingTask::FIELD_SOLID_HARD,
                    CuttingTask::FIELD_SOLID_LITE => CuttingTask::FIELD_SOLID_LITE,
                    default => CuttingTask::FIELD_UNDEFINED,
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
                                'order_line_id'  => $line->id,
                                'amount'         => $line->amount,
                                'position'       => $position++,
                                'time'           => $cuttingTimeLabor->getTime(),
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

        return CuttingTask::query()
            ->whereDate('action_at', $date)
            ->count();
        // return CuttingTask::query()->whereDate('action_at', $date)->max('position');
    }


    /**
     * __ Возвращаем следующую производственную смену
     * @param ManufactureDayAndChange|Carbon|string $manufactureEntity
     * @param int|null $change
     * @return ManufactureDayAndChange
     */
    public static function getNextChange(
        ManufactureDayAndChange|Carbon|string $manufactureEntity,
        int                             $change = null
    ): ManufactureDayAndChange
    {
        $manufDateAndChange = null;
        if ($manufactureEntity instanceof ManufactureDayAndChange) {
            $manufDateAndChange = new ManufactureDayAndChange($manufactureEntity->getManufactureDay()->addDay(), 1);
        } else  {
            $manufDateAndChange = new ManufactureDayAndChange(normalizeToCarbon($manufactureEntity)->addDay(), 1);
            // $manufDateAndChange = new ManufactureDayAndChange(normalizeToCarbon($manufactureEntity)->addDay(), $change);
        }

        return $manufDateAndChange;
    }


    /**
     * ___ Массовое обновление СЗ
     * @param  array  $rows
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
                $setParts[]  = "action_at = CASE ".implode(' ', $casesActionAt)." ELSE action_at END";
                $finalParams = array_merge($finalParams, $paramsActionAt);
            }

            if (!empty($casesPosition)) {
                $setParts[]  = "position = CASE ".implode(' ', $casesPosition)." ELSE position END";
                $finalParams = array_merge($finalParams, $paramsPosition);
            }

            if (empty($setParts)) {
                return;
            }

            $wherePlaceholders = implode(',', array_fill(0, count($allIds), '?'));
            $sql               = "UPDATE {$table} SET ".implode(', ', $setParts)." WHERE id IN ({$wherePlaceholders})";

            // __ Соединяем параметры: параметры CASE1 + параметры CASE2 + параметры WHERE
            DB::update($sql, array_merge($finalParams, $allIds));
        });

    }



}
