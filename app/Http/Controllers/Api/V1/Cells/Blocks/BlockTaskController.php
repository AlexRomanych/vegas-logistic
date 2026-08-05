<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacture\Blocks\Sync\SyncBlockTasksRequest;
use App\Http\Resources\Manufacture\Cells\Blocks\Manage\BlockTaskLineResource;
use App\Http\Resources\Manufacture\Cells\Blocks\Manage\BlockTaskResource;
use App\Models\Manufacture\Cells\Block\BlockCollection;
use App\Models\Manufacture\Cells\Block\BlockDay;
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

            $blockTasks = BlocksService::getBlockTasksByDatesAndStatus($start, $end);
            //
            //// __ 1. Получаем BlockTasks с их строками за нужный период (Запросы 1 и 2)
            //$blockTasks = BlockTask::query()
            //    ->whereBetween('action_at', [
            //        $start->startOfDay(),
            //        $end->endOfDay()
            //    ])
            //    ->with([
            //        'order',
            //        'order.client',
            //        'order.orderType',
            //        'statuses',
            //        'blockLines',
            //        'blockLines.block',
            //        'blockLines.block.blockCollection',
            //        'blockLines.block.blockCollection.kdbDoc',
            //        //'blockLines.block.blockCollection' => function ($query) {
            //        //    $query->select('*')->with('kdbDoc');
            //        //},
            //
            //    ])
            //    ->orderBy('action_at')
            //    ->get();
            //
            //// __ 2. Достаем ВСЕ строки BlockTaskLine изо ВСЕХ задач в один плоский список
            //$allBlockTaskLines = $blockTasks->flatMap(fn($task) => $task->blockLines);
            //
            //// __ 3. Собираем уникальные ID из объектов внутри JSON
            //$allOrderLineIds = $allBlockTaskLines->flatMap(function ($line) {
            //    // Превращаем массив объектов в коллекцию и достаем только 'order_line_id'
            //    return collect($line->order_line_ids ?? [])->pluck('order_line_id');
            //})->unique()->filter()->toArray();
            //
            //// __ 4. Загружаем все OrderLine одним запросом
            //if (!empty($allOrderLineIds)) {
            //    $orderLines = OrderLine::query()
            //        ->whereIn('id', $allOrderLineIds)
            //        ->get()
            //        ->keyBy('id');
            //} else {
            //    $orderLines = collect();
            //}
            //
            //// __ 5. «Прошиваем» OrderLine в память для каждой строки BlockTaskLine
            //$allBlockTaskLines->each(function ($line) use ($orderLines) {
            //    $lineIds = collect($line->order_line_ids ?? [])->pluck('order_line_id');
            //
            //    $associatedOrderLines = collect($lineIds)
            //        ->map(fn($id) => $orderLines->get($id))
            //        ->filter(); // Убираем null, если запись удалена
            //
            //    // __ Заселяем отношение 'orderLines' прямо в памяти
            //    $line->setRelation('orderLines', $associatedOrderLines);
            //});


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
                    'blockLines.block.blockCollection',
                    'blockLines.block.blockCollection.kdbDoc',
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

                            // __ Если при создании СЗ сразу передали смену, пишем в реплику
                            if (isset($diff['taskChanges']['change']['new'])) {
                                $newTask->change = $diff['taskChanges']['change']['new'];
                            }

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
                                'change'    => $diff['taskChanges']['change']['new'] ?? null,
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
                                    'change'    => $diff['taskChanges']['change']['new'] ?? null,
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

                        // ⚠️ Внимание: тут в исходном коде была опечатка (usort($diffs вместо $lineDiffs)), исправил
                        usort($lineDiffs, function ($a, $b) {
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
                                    if (isset($lineDiff['amount'])) {
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
                                    if (isset($lineDiff['amount'])) {
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
                        $blockTaskLine = BlockTaskLine::query()->with('block.blockCollection')->findOrFail($lineCalcId);

                        $block = $blockTaskLine->block;
                        if (!$block) {
                            throw new Exception('Missing Block with in BlockTaskLine with $id = ' . $lineCalcId);
                        }

                        // __ Получаем так, потому что collection - поле в Block
                        $collection = $block->getRelation('blockCollection');

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

            $casesChange  = [];
            $paramsChange = [];

            foreach ($rows as $row) {
                if (isset($row['action_at'])) {
                    $casesActionAt[] = "WHEN id = ? THEN ?";
                    array_push($paramsActionAt, $row['id'], $row['action_at']);
                }

                if (isset($row['position'])) {
                    $casesPosition[] = "WHEN id = ? THEN ?";
                    array_push($paramsPosition, $row['id'], $row['position']);
                }

                if (isset($row['change'])) {
                    $casesChange[] = "WHEN id = ? THEN ?";
                    array_push($paramsChange, $row['id'], $row['change']);
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

            // __ Интегрируем блок смены в итоговый
            // __ change обернуто в косые кавычки: `change` = CASE .... Это сделано обязательно, MySQL
            // __ change обернуто в двойные кавычки: "change" = CASE .... Это сделано обязательно, PostgreSQL
            // __ так как слово CHANGE является зарезервированной системной командой в SQL
            if (!empty($casesChange)) {
                $setParts[]  = '"change" = CASE ' . implode(' ', $casesChange) . ' ELSE "change" END';
                $finalParams = array_merge($finalParams, $paramsChange);
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
     * ___ Изменяем смену в сз
     * @param Request $request
     * @return string
     */
    public function modifyChange(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'     => 'required|integer|exists:block_tasks,id',
                'change' => 'required|string|in:' .
                    BlockDay::CHANGE_1 . ',' .
                    BlockDay::CHANGE_2,
            ]);

            $blockTask = BlockTask::query()->find($validated['id']);
            if (!$blockTask) {
                throw new Exception('Missing block task with id: ' . $validated['id'] . '.');
            }

            $blockTask->change = $validated['change'];
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
            //$all = $request->all();

            $validated = $request->validate([
                // __ Проверяем, что 'data' — это обязательный, не пустой массив
                'data'        => 'required|array|min:1',

                // __ Проверяем ID внутри каждого элемента массива
                'data.*.id'   => 'required|integer|exists:block_task_lines,id',

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
            //    'data.*.id' => 'required|integer|exists:block_task_lines,id',
            //    'data.*.table' => ['required', 'string', Rule::in([BlockTaskLine::FIELD_TABLE_1, BlockTaskLine::FIELD_TABLE_2, BlockTaskLine::FIELD_TABLE_3])],
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
            //    UPDATE block_task_lines AS c
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


    /**
     * ___ Проверяем наличие СЗ на Раскрой по статусам в определенную дату
     * @param Request $request
     * @return bool[]|string
     */
    public function checkBlockTasksByStatusOnDate(Request $request)
    {
        try {
            $validated = $request->validate([
                // __ Проверяем, что 'date' — это дата
                'date'       => 'required|date_format:Y-m-d',
                // __ Проверяем, что 'change' — это смена
                'change'     => 'required|string|in:1,2',
                // __ Проверяем, что 'statuses' — это массив
                'statuses'   => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*' => 'integer|exists:block_task_statuses,id',
            ]);

            $data        = $validated['statuses'] ?? null;
            $action_date = Carbon::parse($validated['date'])->startOfDay();

            $blockTasks = BlockTask::query()
                ->where('change', $validated['change'])
                ->whereDate('action_at', $action_date)
                ->byStatus($data)
                ->get();

            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!

            return ['data' => !$blockTasks->isEmpty()];
            //return BlockTaskResource::collection($blockTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем СЗ для Блоков
     * @param Request $request
     * @return string
     */
    public function deleteBlockTasksByOrderId(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:orders,id'
            ]);

            DB::transaction(function () use ($validated) {
                // __ Меняем позиции СЗ в днях, где удаляем
                $deletedTasks = BlockTask::query()
                    ->select(['id', 'action_at'])
                    ->where('order_id', $validated['id'])
                    ->get();

                // __ Удаляем здесь
                BlockTask::query()
                    ->where('order_id', $validated['id'])
                    ->delete();

                foreach ($deletedTasks as $deletedTask) {
                    $tasksToUpdate = [];
                    $pos           = 1;
                    $existTasks    = BlockTask::query()
                        ->select(['id', 'action_at', 'position'])
                        ->where('action_at', '>=', $deletedTask->action_at->startOfDay())
                        ->where('action_at', '<=', $deletedTask->action_at->endOfDay())
                        ->orderBy('position')
                        ->get();

                    foreach ($existTasks as $existTask) {
                        $tasksToUpdate[] = [
                            'id'        => $existTask->id,
                            'action_at' => null,
                            'position'  => $pos++,
                        ];
                    }

                    BlocksService::bulkUpdateTasks($tasksToUpdate);
                }
            });

            return EndPointStaticRequestAnswer::ok('СЗ успешно удалено');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Добавляем СЗ для Блоков
     * @param Request $request
     * @return string
     */
    public function addBlockTasksByOrderId(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:orders,id'
            ]);

            BlocksService::createBlockTaskFromOrderId($validated['id']);

            return EndPointStaticRequestAnswer::ok('СЗ успешно создано');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем СЗ на Раскрой по статусам
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getBlockTasksByStatus(Request $request)
    {
        try {
            //$all = $request->all();

            $validated = $request->validate([
                // __ Проверяем, что 'statuses' — это массив
                'statuses'   => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*' => 'integer|exists:block_task_statuses,id',
            ]);

            $data       = $validated['statuses'] ?? null;
            $blockTasks = BlockTask::query()
                ->byStatus($data)
                // ->whereBetween('action_at', [
                //     $start->startOfDay(),
                //     $end->endOfDay()
                // ])
                // ->whereDate('action_at', '>=', $start)     // Используем такую конструкцию, потому что
                // ->whereDate('action_at', '<=', $end)       // ->whereBetween() не включает периоды
                ->with([
                    'order',
                    'order.client',
                    'order.orderType',
                    'statuses',
                    'blockLines',
                    'blockLines.block',
                    'blockLines.block.blockCollection',
                    'blockLines.block.blockCollection.kdbDoc',
                ])
                ->orderBy('action_at')
                ->get();


            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!


            return BlockTaskResource::collection($blockTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    //___ Получаем СЗ на Блоки по статусам до определенной даты и смены
    public function getBlockTasksByStatusBeforeDateAndChange(Request $request)
    {
        try {
            $validated = $request->validate([
                // __ Проверяем, что 'date' — это дата
                'date'       => 'required|date_format:Y-m-d',
                // __ Проверяем, что 'change' — это смена
                'change'     => 'required|string|in:1,2',
                // __ Проверяем, что 'statuses' — это массив
                'statuses'   => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*' => 'integer|exists:block_task_statuses,id',
            ]);

            $data        = $validated['statuses'] ?? null;
            $change      = $validated['change'];
            $action_date = Carbon::parse($validated['date'])->startOfDay();

            $blockTasks = BlockTask::query()
                ->where(function ($query) use ($action_date, $change) {
                    // __ Условие 1: Все задачи до текущей даты (для обеих смен)
                    $query->whereDate('action_at', '<', $action_date);

                    // __ Условие 2: Если смена 2 — добавляем задачи первой смены за текущую дату
                    if ($change === '2') {
                        $query->orWhere(function ($q) use ($action_date) {
                            $q->whereDate('action_at', $action_date)
                                ->where('change', '1');
                        });
                    }
                })
                ->byStatus($data)
                ->with([
                    'order',
                    'order.client',
                    'order.orderType',
                    'statuses',
                    'blockLines',
                    //'blockLines.block',
                    //'blockLines.block.blockCollection',
                    //'blockLines.block.blockCollection.kdbDoc',
                ])
                ->orderBy('action_at')
                ->get();


            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!


            return BlockTaskResource::collection($blockTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем СЗ на Блоки по статусам и периоду
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getBlockTasksByStatusAndPeriod(Request $request)
    {
        try {
            //$all = $request->all();
            $validated = $request->validate([
                // __ Проверяем, что 'statuses' — это массив
                'statuses'     => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*'   => 'integer|exists:block_task_statuses,id',
                //'status'       => 'nullable|numeric|in:1,2,3,4,5',
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end   = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodBlockTaskArchive();
                $start  = Carbon::parse($period->getStart());
                $end    = Carbon::parse($period->getEnd());
            }

            $status = $validated['statuses'] ?? null;

            $blockTasks = BlocksService::getBlockTasksByDatesAndStatus($start, $end, $status);

            return BlockTaskResource::collection($blockTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Устанавливаем статус Выполнено для линии
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setBlockTaskLinesDone(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'required|integer|exists:block_task_lines,id',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = BlockTaskLine::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing block task line with id: ' . $id . '.');
                }

                $line->finished_at = now();
                $line->save();
            }

            $lines = BlockTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return BlockTaskLineResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Устанавливаем статус Не выполнено для линии
     * @param Request $request
     * @return AnonymousResourceCollection|string
     * @noinspection DuplicatedCode
     */
    public function setBlockTaskLinesFalse(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'    => 'required|array',
                'ids.*'  => 'required|integer|exists:block_task_lines,id',
                'reason' => 'required|string',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = BlockTaskLine::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing block task line with id: ' . $id . '.');
                }

                $line->false_at     = now();
                $line->false_reason = $validated['reason'];

                $history = $line->false_history;
                if (is_null($history)) {
                    $history = [];
                }

                $history[]           = [
                    'at'     => $line->false_at->format(RETURN_DATE_TIME_FORMAT),
                    'by'     => auth()->id(),
                    'reason' => $validated['reason'],
                ];
                $line->false_history = $history;
                $line->finished_at   = null;
                $line->save();
            }

            $lines = BlockTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return BlockTaskLineResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Сбрасываем отметку Выполнено/Не выполнено для линии
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setBlockTaskLinesReset(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'required|integer|exists:block_task_lines,id',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = BlockTaskLine::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing block task line with id: ' . $id . '.');
                }

                $line->finished_at  = null;
                $line->false_at     = null;
                $line->false_reason = null;
                $line->save();
            }

            $lines = BlockTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return BlockTaskLineResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Устанавливает новую дату (action_at) Сменному Заданию
     * @param Request $request
     * @return string
     */
    public function setBlockTaskActionAt(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'   => 'required|integer|exists:block_tasks,id',
                'date' => 'present|nullable|string|date_format:Y-m-d',
            ]);

            //$targetDate = is_null($validated['date']) ? Carbon::now() : Carbon::parse($validated['date']);

            //$targetDate = is_null($validated['date'])
            //    ? Carbon::now()->startOfDay()->format(RETURN_DATE_TIME_FORMAT)
            //    : Carbon::parse($validated['date'])->format(RETURN_DATE_TIME_FORMAT);

            DB::transaction(function () use ($validated) {
                // __ Получаем само СЗ и его старую дату + применяем изменения
                $blockTask = BlockTask::query()->find($validated['id']);
                $oldDate   = $blockTask->action_at;
                // __ Устанавливаем позицию в отрицательную зону, так как наверняка в день, куда перемещаем уже есть такая позиция
                $blockTask->position  = -1 * $blockTask->id;
                $blockTask->action_at = Carbon::parse($validated['date'])->startOfDay()->format(RETURN_DATE_TIME_FORMAT);
                $blockTask->save();

                // __ Получаем все СЗ за день, из которого убираем СЗ
                $blockTasksFrom = BlockTask::query()
                    ->whereDayAt($oldDate)
                    ->orderBy('position')
                    ->get();

                // __ Создаем производственный день или получаем его, если он уже существует
                // __ Идея - создать новый день, если он не существует
                $day = BlockDay::findOrCreateByDateAndChange($validated['date']);

                // __ Получаем все СЗ за день, в которое добавляем СЗ c уже измененной датой
                $blockTasksTo = BlockTask::query()
                    ->whereDayAt($validated['date'])
                    ->orderBy('position')
                    ->get();

                // __ Перемещаем СЗ в новый день в конец списка, так благодарая отрицательному position и orderBy он будет в самом начале
                // __ Проверяем, что коллекция не пустая
                if ($blockTasksTo->isNotEmpty()) {
                    // shift() удаляет первый элемент из коллекции и возвращает его
                    $firstItem = $blockTasksTo->shift();
                    // push() добавляет этот элемент в самый конец коллекции
                    $blockTasksTo->push($firstItem);
                }

                // debug
                //$blockTasksFromArray = $blockTasksFrom->toArray();
                //$blockTasksToArray = $blockTasksTo->toArray();


                // __ Теперь формируем данные для обновления с учетом position

                // __ Тот день, из которого убираем СЗ
                for ($i = 0; $i < 2; $i++) {
                    $blockTasks = $i == 0 ? $blockTasksFrom : $blockTasksTo;

                    $tasksToUpdate = [];
                    $position      = 1;
                    foreach ($blockTasks as $task) {
                        if ($task->position !== $position) {
                            $tasksToUpdate[] = [
                                'id'        => $task->id,
                                'action_at' => null,        // оставляем дату прежней
                                'position'  => $position,
                            ];
                        };
                        $position++;
                    }

                    // __ Применяем изменения
                    BlocksService::bulkUpdateTasks($tasksToUpdate);
                }
            });

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем Комментарий Записи
     * @param Request $request
     * @return string
     */
    public function setBlockTaskLineDescription(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'          => 'required|integer|exists:block_task_lines,id',
                'description' => 'nullable|string',
            ]);

            $description = $validated['description'] ?? null;
            BlockTaskLine::query()
                ->where('id', $validated['id'])
                ->update(['description' => $description]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
