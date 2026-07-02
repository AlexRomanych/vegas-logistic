<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacture\Blocks\Sync\SyncBlockTasksRequest;
use App\Http\Resources\Manufacture\Cells\Blocks\Manage\BlockTaskResource;
use App\Models\Manufacture\Cells\Block\BlockCollection;
use App\Models\Manufacture\Cells\Block\BlockTask;
use App\Models\Manufacture\Cells\Block\BlockTaskLine;
use App\Models\Manufacture\Cells\Block\BlockTaskStatus;
use App\Models\Order\OrderLine;
use App\Services\DefaultsService;
use App\Services\Manufacture\BlocksService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class BlockTaskController extends Controller
{
    /**
     * ___ Получаем СЗ на Раскрой
     * @param Request $request
     * @return AnonymousResourceCollection|string
     * @noinspection DuplicatedCode
     */
    public function getBlockTasks(Request $request)
    {
        try {
            $validated = $request->validate([
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end   = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodPlanLoads();
                $start  = Carbon::parse($period->getStart());
                $end    = Carbon::parse($period->getEnd());
            }

            // __ 1. Получаем BlockTasks с их строками за нужный период (Запросы 1 и 2)
            $blockTasks = BlockTask::query()
                ->whereBetween('action_at', [
                    $start->startOfDay(),
                    $end->endOfDay()
                ])
                ->with([
                    'order',
                    'order.client',
                    'order.orderType',
                    'statuses',
                    'blockLines',
                    'blockLines.block',
                    'blockLines.block.collection',
                ])
                ->orderBy('action_at')
                ->get();

            // __ 2. Достаем ВСЕ строки BlockTaskLine изо ВСЕХ задач в один плоский список
            $allBlockTaskLines = $blockTasks->flatMap(fn($task) => $task->blockLines);

            // __ 3. Собираем уникальные ID из объектов внутри JSON
            $allOrderLineIds = $allBlockTaskLines->flatMap(function ($line) {
                // Превращаем массив объектов в коллекцию и достаем только 'order_line_id'
                return collect($line->order_line_ids ?? [])->pluck('order_line_id');
            })->unique()->filter()->toArray();

            // __ 4. Загружаем все OrderLine одним запросом
            if (!empty($allOrderLineIds)) {
                $orderLines = OrderLine::query()
                    ->whereIn('id', $allOrderLineIds)
                    ->get()
                    ->keyBy('id');
            } else {
                $orderLines = collect();
            }

            // __ 5. «Прошиваем» OrderLine в память для каждой строки BlockTaskLine
            $allBlockTaskLines->each(function ($line) use ($orderLines) {

                $lineIds = collect($line->order_line_ids ?? [])->pluck('order_line_id');

                $associatedOrderLines = collect($lineIds)
                    ->map(fn($id) => $orderLines->get($id))
                    ->filter(); // Убираем null, если запись удалена

                // __ Заселяем отношение 'orderLines' прямо в памяти
                $line->setRelation('orderLines', $associatedOrderLines);
            });


            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция - Реализовано через Middleware
            // !!!!!!!!!!!!!!!!!!!!!


            return BlockTaskResource::collection($blockTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем СЗ на Раскрой
     * @param string $orderId
     * @return AnonymousResourceCollection|string
     * @noinspection DuplicatedCode
     */
    public function getBlockTasksByOrderId(string $orderId)
    {
        try {
            $validated = Validator::make([
                'id' => $orderId
            ], [
                'id' => 'required|exists:orders,id'
            ])
                ->validate();

            $blockTasks = BlockTask::query()
                ->where('order_id', $validated['id'])
                ->with([
                    'order',
                    'order.client',
                    'order.orderType',
                    'statuses',
                    'blockLines',
                    'blockLines.block',
                    'blockLines.block.collection',
                ])
                ->orderBy('action_at')
                ->get();

            // __ 2. Достаем ВСЕ строки BlockTaskLine изо ВСЕХ задач в один плоский список
            $allBlockTaskLines = $blockTasks->flatMap(fn($task) => $task->blockLines);

            // __ 3. Собираем уникальные ID из объектов внутри JSON
            $allOrderLineIds = $allBlockTaskLines->flatMap(function ($line) {
                // Превращаем массив объектов в коллекцию и достаем только 'order_line_id'
                return collect($line->order_line_ids ?? [])->pluck('order_line_id');
            })->unique()->filter()->toArray();

            // __ 4. Загружаем все OrderLine одним запросом
            if (!empty($allOrderLineIds)) {
                $orderLines = OrderLine::query()
                    ->whereIn('id', $allOrderLineIds)
                    ->get()
                    ->keyBy('id');
            } else {
                $orderLines = collect();
            }

            // __ 5. «Прошиваем» OrderLine в память для каждой строки BlockTaskLine
            $allBlockTaskLines->each(function ($line) use ($orderLines) {

                $lineIds = collect($line->order_line_ids ?? [])->pluck('order_line_id');

                $associatedOrderLines = collect($lineIds)
                    ->map(fn($id) => $orderLines->get($id))
                    ->filter(); // Убираем null, если запись удалена

                // __ Заселяем отношение 'orderLines' прямо в памяти
                $line->setRelation('orderLines', $associatedOrderLines);
            });


            return BlockTaskResource::collection($blockTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем СЗ на Раскрой
     * @param SyncBlockTasksRequest $request
     * @return mixed|string
     * @noinspection DuplicatedCode
     */
    public function updateBlockTasks(SyncBlockTasksRequest $request)
    {
        // !!! TODO: SyncBlockTasksRequest
        try {
            $idMap = []; // __ Для возврата соответствия temp_id => real_id

            return DB::transaction(function () use ($request, &$idMap) {
                $tasksToUpdate = [];

                $linesToRecalcTime = [];

                // __ Сортируем именно в таком порядке, удаляем в самом конце
                $diffs = $request->validated()['diffs'];
                usort($diffs, function ($a, $b) {
                    // Назначаем приоритеты: чем меньше число, тем выше элемент в списке
                    $priorities = fn($type) => match ($type) {
                        'ADDED'   => 1,
                        'UPDATED' => 2,
                        'DELETED' => 3,
                        default   => 4,
                    };

                    return $priorities($a['type']) <=> $priorities($b['type']);
                });


                foreach ($diffs as $diff) {
                    $currentTaskId = null;  // __ Маяк созданного СЗ (Если флаг в lines - ADDED и в tasks - ADDED)
                    $updatedTaskId = null;  // __ Маяк обновляемого СЗ (Если флаг в lines - ADDED, но эти lines приходят из DELETED task и в tasks - UPDATED)

                    // --- 1. ОБРАБОТКА ЗАДАЧИ ---

                    switch ($diff['type']) {
                        case 'ADDED':

                            // __ Создаем новую задачу
                            $newTask = BlockTask::query()
                                ->findOrFail($diff['taskIdRef'])
                                ->replicate();

                            // __ Устанавливаем позицию в отрицательную зону
                            // __ танцы "с бубнами" из-за ограничения пар ключей - устанавливаем уникальную позицию
                            $newTask->position *= -1;
                            $newTask->save();
                            $newTask->position = $newTask->id * (-1);
                            $newTask->save();

                            // __ Создаем запись в Статусе: Создано
                            $newTask->statuses()->attach([
                                BlockTaskStatus::BLOCK_STATUS_CREATED_ID => [
                                    'set_at'     => now(),
                                    'created_by' => auth()->id(),
                                ]
                            ]);

                            $tasksToUpdate[] = [
                                'id'        => $newTask->id,
                                'action_at' => $diff['taskChanges']['action_at']['new'] ?? null,
                                'position'  => $diff['taskChanges']['position']['new'] ?? null,
                            ];

                            $idMap[$diff['taskId']] = $newTask->id;
                            $currentTaskId          = $newTask->id;

                            break;

                        case 'UPDATED':

                            // $task = BlockTask::query()->findOrFail($diff['taskId']);

                            if (!empty($diff['taskChanges'])) {
                                $tasksToUpdate[] = [
                                    'id'        => $diff['taskId'],
                                    'action_at' => $diff['taskChanges']['action_at']['new'] ?? null,
                                    'position'  => $diff['taskChanges']['position']['new'] ?? null,
                                ];
                            }

                            $currentTaskId = $diff['taskId'];
                            // $updatedTaskId = $diff['taskId'];
                            break;

                        case 'DELETED':
                            BlockTask::destroy($diff['taskId']);
                            break;
                    }


                    // --- 2. ОБРАБОТКА СТРОК (LINES) ---
                    if (!empty($diff['lineChanges'])) {
                        $linesToUpdate = [];

                        // __ Сортируем именно в таком порядке, удаляем в самом конце
                        $lineDiffs = $diff['lineChanges'];
                        usort($diffs, function ($a, $b) {
                            // __ Назначаем приоритеты: чем меньше число, тем выше элемент в списке
                            $priorities = fn($type) => match ($type) {
                                'ADDED'   => 1,
                                'UPDATED' => 2,
                                'DELETED' => 3,
                                default   => 4,
                            };

                            return $priorities($a['type']) <=> $priorities($b['type']);
                        });


                        foreach ($lineDiffs as $lineDiff) {
                            switch ($lineDiff['type']) {
                                case 'ADDED':

                                    $newLine = BlockTaskLine::query()
                                        ->findOrFail($lineDiff['lineIdRef'])
                                        ->replicate();

                                    // __ Ситуация, когда новые строки появились в новом СЗ
                                    // __ Связываем с новым СЗ
                                    if ($currentTaskId) {
                                        $newLine->block_task_id = $currentTaskId;
                                    }

                                    // __ Устанавливаем позицию в отрицательную зону
                                    // __ танцы "с бубнами" из-за ограничения пар ключей - устанавливаем уникальную позицию
                                    $newLine->position *= -1;
                                    $newLine->save();
                                    $newLine->position = $newLine->id * (-1);
                                    $newLine->save();

                                    $linesToUpdate[] = [
                                        'id'       => $newLine->id,
                                        'amount'   => $lineDiff['amount']['new'] ?? null,
                                        'position' => $lineDiff['position']['new'] ?? null,
                                    ];

                                    $idMap[$lineDiff['lineId']] = $newLine->id;

                                    // __ Собираем id линий для пересчета трудозатрат
                                    if (isset( $lineDiff['amount'])) {
                                        $linesToRecalcTime[] = $newLine->id;
                                    }

                                    break;

                                case 'UPDATED':

                                    $linesToUpdate[] = [
                                        'id'       => $lineDiff['lineId'],
                                        'amount'   => $lineDiff['amount']['new'] ?? null,
                                        'position' => $lineDiff['position']['new'] ?? null,
                                        // 'block_task_id' => $updatedTaskId ?? null,
                                    ];

                                    // __ Собираем id линий для пересчета трудозатрат
                                    if (isset( $lineDiff['amount'])) {
                                        $linesToRecalcTime[] = $lineDiff['lineId'];
                                    }

                                    break;

                                case 'DELETED':
                                    BlockTaskLine::destroy($lineDiff['lineId']);
                                    break;
                            }
                        }
                    }

                    // __ Выполняем массовое обновление строк, если они есть
                    if (!empty($linesToUpdate)) {
                        $this->bulkUpdateLines($linesToUpdate);
                    }
                }

                // __ Выполняем массовое обновление СЗ, если они есть
                if (!empty($tasksToUpdate)) {
                    $this->bulkUpdateTasks($tasksToUpdate);
                }

                // __ Смотрим, если еще прилетел статус, который нужно установить для СЗ,
                // __ то устанавливаем его
                foreach ($diffs as $diff) {
                    if (isset($diff['taskChanges']) && /*!is_null($diff['taskChanges']) &&*/ !is_null($diff['taskChanges']['status'])) {
                        // __ Пропускаем тот случай, кагда с фронта прилетает создание нового СЗ (ADDED)
                        // __ Это обрабатываем выше
                        if ($diff['taskId'] !== 0) {
                            // __ Меняем статус только в случае, если нужно установить статус "Выполняется"
                            // __ Случай, когда перетасктваем СЗ в день, гже есть статус "Выполняется"
                            // __ Остальное не трогаем, потому что начинаются проблемы, такой костыль получился
                            if ($diff['taskChanges']['status']['new'] === BlockTaskStatus::BLOCK_STATUS_RUNNING_ID) {
                                $setTask = BlockTask::query()->findOrFail($diff['taskId']);
                                $setTask->statuses()->attach([
                                    $diff['taskChanges']['status']['new'] => [
                                        'set_at'     => Carbon::now(),
                                        'created_by' => auth()->id(),
                                    ]
                                ]);
                            }
                        }
                    }
                }


                // __ Пересчитываем трудозатраты при разделении количества
                if (count($linesToRecalcTime) !== 0) {
                    foreach ($linesToRecalcTime as $lineCalcId) {
                        $blockTaskLine = BlockTaskLine::query()->with('block.collection')->findOrFail($lineCalcId);

                        $block = $blockTaskLine->block;
                        if (!$block) {
                            throw new Exception('Missing Block with in BlockTaskLine with $id = ' . $lineCalcId);
                        }

                        // __ Получаем так, потому что collection - поле в Block
                        $collection = $block->getRelation('collection');

                        $blockTaskLine->time = $collection->productivity !== 0.0
                            ? ($block->length * $block->width / 100 / 100) * $blockTaskLine->amount / $collection->productivity
                            : 0;
                        $blockTaskLine->save();
                    }
                }

                return EndPointStaticRequestAnswer::ok();

                // TODO: Разобраться с ответом
                // return response()->json([
                //     'status' => 'success',
                //     'idMap'  => $idMap
                // ]);
            });
        } catch (Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Массовое обновление СЗ
     * @param array $rows
     * @return void
     * @throws Throwable
     * @noinspection DuplicatedCode
     */
    private function bulkUpdateTasks(array $rows)
    {
        // __ Получаем имя таблицы
        $table = (new BlockTask)->getTable();

        // __ 1. Находим только те ID, у которых действительно меняется позиция (чтобы не уводить в минус лишнее)
        $idsForMinus = array_column(array_filter($rows, fn($r) => isset($r['position'])), 'id');

        // __ 2. Находим все ID, которые участвуют в обновлении (хоть позиция, хоть amount)
        $allIds = array_column($rows, 'id');

        DB::transaction(function () use ($table, $rows, $idsForMinus, $allIds) {
            // Warning!!! Не работает. Уводит position в минус.
            // !!! В чем проблема сейчас?
            // !!! Твой «ШАГ 1» уводит записи в минус на текущей дате (action_at). Если на новой дате, куда ты хочешь перенести задачу,
            // !!!уже есть запись с такой же позицией, ты все равно получишь Unique violation.
            // !!! Решение: Полное «обнуление» конфликта
            // !!! Чтобы гарантированно избежать проблем, тебе нужно на первом шаге временно сделать записи уникальными не только по позиции, но и по дате,
            // !!! чтобы они вообще не пересекались ни с какими существующими данными.
            //$placeholders = implode(',', array_fill(0, count($allIds), '?'));
            //DB::update("UPDATE {$table} SET position = (id * -1) WHERE id IN ({$placeholders})", $allIds);


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
     * ___ Массовое обновление Записей через один запрос (Raw SQL Case)
     * @param array $rows
     * @return void
     * @throws Throwable
     * @noinspection DuplicatedCode
     */
    private function bulkUpdateLines(array $rows)
    {
        // __ Получаем имя таблицы
        $table = (new BlockTaskLine)->getTable();

        // __ 1. Находим только те ID, у которых действительно меняется позиция (чтобы не уводить в минус лишнее)
        $rowsWithPosition = array_filter($rows, fn($row) => !is_null($row['position'] ?? null));
        $idsForMinus      = array_column($rowsWithPosition, 'id');

        // __ 2. Находим все ID, которые участвуют в обновлении (хоть позиция, хоть amount)
        $allIds = array_column($rows, 'id');

        DB::transaction(function () use ($table, $rows, $idsForMinus, $allIds) {
            // __ ШАГ 1: Уводим в минус ТОЛЬКО те записи, где позиция реально будет обновлена
            if (!empty($idsForMinus)) {
                $minusPlaceholders = implode(',', array_fill(0, count($idsForMinus), '?'));
                DB::update(
                    "UPDATE {$table} SET position = (id * -1) WHERE id IN ({$minusPlaceholders})",
                    $idsForMinus
                );
            }

            // __ ШАГ 2: Собираем финальный запрос
            $casesAmount    = [];
            $paramsAmount   = [];
            $casesPosition  = [];
            $paramsPosition = [];
            // $casesTaskId    = [];
            // $paramsTaskId   = [];

            foreach ($rows as $row) {
                if (isset($row['amount'])) {
                    $casesAmount[] = "WHEN id = ? THEN ?";
                    array_push($paramsAmount, $row['id'], $row['amount']);
                }
                if (isset($row['position'])) {
                    $casesPosition[] = "WHEN id = ? THEN ?";
                    array_push($paramsPosition, $row['id'], $row['position']);
                }
                // if (isset($row['block_task_id'])) {
                //     $casesTaskId[] = "WHEN id = ? THEN ?";
                //     array_push($paramsTaskId, $row['id'], $row['block_task_id']);
                // }
            }

            $setParts    = [];
            $finalParams = [];

            if (!empty($casesAmount)) {
                $setParts[]  = "amount = CASE " . implode(' ', $casesAmount) . " ELSE amount END";
                $finalParams = array_merge($finalParams, $paramsAmount);
            }

            if (!empty($casesPosition)) {
                $setParts[]  = "position = CASE " . implode(' ', $casesPosition) . " ELSE position END";
                $finalParams = array_merge($finalParams, $paramsPosition);
            }

            // if (!empty($casesTaskId)) {
            //     $setParts[]  = "block_task_id = CASE ".implode(' ', $casesTaskId)." ELSE block_task_id END";
            //     $finalParams = array_merge($finalParams, $paramsTaskId);
            // }

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
     * ___ Устанавливает комментарий к Сменному Заданию
     * @param Request $request
     * @return string
     */
    public function setBlockTaskComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'      => 'required|integer|exists:block_tasks,id',
                'comment' => 'present|nullable|string',
            ]);

            $blockTask = BlockTask::query()->find($validated['id']);
            if (!$blockTask) {
                throw new Exception('Missing block task with id: ' . $validated['id'] . '.');
            }

            $blockTask->comment = $validated['comment'];
            $blockTask->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Меняем Линию производства Блоков
     * @param Request $request
     * @return string
     */
    public function taskLinesManufLineSet(Request $request)
    {
        try {
            $all = $request->all();

            $validated = $request->validate([
                // __ Проверяем, что 'data' — это обязательный, не пустой массив
                'data'         => 'required|array|min:1',

                // __ Проверяем ID внутри каждого элемента массива
                'data.*.id'    => 'required|integer|exists:block_task_lines,id',

                // __ Проверяем строку 'table' на соответствие конкретным значениям
                'data.*.line' => [
                    'required',
                    'string',
                    Rule::in([
                        (string)BlockCollection::LINE_1,
                        (string)BlockCollection::LINE_2,
                    ]),
                ],
            ]);

            // __ Валидация с кастомными сообщениями
            //$validated = $request->validate([
            //    'data' => 'required|array|min:1',
            //    'data.*.id' => 'required|integer|exists:cutting_task_lines,id',
            //    'data.*.table' => ['required', 'string', Rule::in([CuttingTaskLine::FIELD_TABLE_1, CuttingTaskLine::FIELD_TABLE_2, CuttingTaskLine::FIELD_TABLE_3])],
            //], [
            //    'data.required' => 'Массив данных обязателен для заполнения.',
            //    'data.min' => 'Массив данных не должен быть пустым.',
            //    'data.*.id.exists' => 'Выбранный ID задачи не существует в базе данных.',
            //    'data.*.table.in' => 'Поле table должно принимать значения: table_1, table_2 или table_3.',
            //]);

            $data = $validated['data'];
            DB::transaction(function () use ($data) {
                foreach ($data as $item) {
                    $line = BlockTaskLine::query()->find($item['id']);
                    if (!$line) {
                        throw new Exception('Missing block task line with id: ' . $item['id'] . '.');
                    }
                    $line->line = $item['line'];
                    $line->save();
                }
            });


            // __ Скрипт для обновления одним запросом
            //// Строим сырой запрос для массового обновления (Bulk Update)
            //// Формируем плейсхолдеры (?, ?) для каждого элемента массива данных
            //$valuePairs = array_map(function () {
            //    return '(?, ?)';
            //}, $data);
            //
            //$valuesSql = implode(', ', $valuePairs);
            //
            //// Собираем все значения в один плоский массив для безопасной привязки параметров (SQL Injection Protection)
            //$bindings = [];
            //foreach ($data as $item) {
            //    $bindings[] = $item['id'];
            //    $bindings[] = $item['table'];
            //}
            //
            //// Итоговый SQL-запрос для PostgreSQL
            ///** @noinspection SqlDialectInspection */
            //$query = "
            //    UPDATE cutting_task_lines AS c
            //    SET \"table\" = v.new_table
            //    FROM (VALUES {$valuesSql}) AS v(id, new_table)
            //    WHERE c.id = CAST(v.id AS INTEGER)
            //";
            //
            //// Выполняем одним запросом внутри транзакции
            //DB::transaction(function () use ($query, $bindings) {
            //    DB::update($query, $bindings);
            //});


            return EndPointStaticRequestAnswer::ok('Изменено успешно');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
