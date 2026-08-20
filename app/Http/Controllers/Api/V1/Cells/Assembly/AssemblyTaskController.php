<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacture\Assembly\GetAssemblyTasksRequest;
use App\Http\Requests\Manufacture\Assembly\Sync\SyncAssemblyTasksRequest;
use App\Http\Resources\Manufacture\Cells\Assembly\Manage\AssemblyTaskResource;
use App\Models\Manufacture\Cells\Assembly\AssemblyDay;
use App\Models\Manufacture\Cells\Assembly\AssemblyTask;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskLine;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskLineSector;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskStatus;
use App\Models\Models\Model;
use App\Services\DefaultsService;
use App\Services\Manufacture\AssemblyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class AssemblyTaskController extends Controller
{
    public function getAssemblyTasks(GetAssemblyTasksRequest $request)
    {
        try {
            // Если передан ID — возвращаем одну конкретную задачу
            //$taskId = $request->validated('id');
            //if (!$taskId = $request->validated('id')) {
            //    return response()->json([
            //        'data' => $taskId
            //    ]);
            //}

            // __ Получаем период
            $periodRequest = $request->validated('period');
            if (isset($periodRequest)) {
                $start = Carbon::parse($periodRequest['start']);
                $end   = Carbon::parse($periodRequest['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodPlanLoads();
                $start  = Carbon::parse($period->getStart());
                $end    = Carbon::parse($period->getEnd());
            }

            // __ Получаем участки производства
            $sectors = $request->getSectors();

            $assemblyTasks = AssemblyTask::query()
                ->whereBetween('action_at', [
                    $start->startOfDay(),
                    $end->endOfDay()
                ])
                ->with([
                    'order',
                    'order.client',
                    'order.orderType',
                    'statuses',
                    // __ Статистика
                    'sectorStatsTotal',
                    'lines.sectorStats',
                ])
                // __ Тут фильтруем по участкам и там же добавляем все связи
                ->sectors($sectors, [
                    'lines.orderLine',
                    'lines.orderLine.model',
                    'lines.orderLine.model.manufactureGroup',
                    //'lines.sectorStats'
                ])

                //->with(['lines.sectors' => function ($query) {
                //    $query->select('assembly_task_line_id', 'sector')
                //        ->selectRaw('SUM(amount) as total_amount')
                //        ->selectRaw('SUM(CASE WHEN finished_at IS NOT NULL THEN amount ELSE 0 END) as finished_amount')
                //        ->groupBy('assembly_task_line_id', 'sector');
                //}])


                ->get();

            // __ Инициализируем query
            //$query = AssemblyTask::query()
            //    ->whereBetween('action_at', [
            //        $start->startOfDay(),
            //        $end->endOfDay()
            //    ])
            //->sectors($sectors);
            //if (is_array($sectors)) {   // __ или [] или ['coconut', 'latex', ...]
            //    if (count($sectors) > 0) {
            //        // __ Получаем СЗ с этими участками ['coconut', 'latex', ...]
            //        $query->sectors($sectors);
            //        //$query->with(['lines.sectors']);
            //    } else {
            //        // __ Получаем СЗ без этих участков sectors === []
            //        $query->with(['lines']);
            //    }
            //} else {
            //    // __ Получаем все СЗ со всеми участками sectors === null
            //    $query->with(['lines', 'lines.sectors']);
            //}
            //$assemblyTasks = $query->get();

            // !!! Отфильтровываем Чехол
            $assemblyTasks = AssemblyService::filterCovers($assemblyTasks);

            //return ['data' => $assemblyTasks->toArray()];

            return AssemblyTaskResource::collection($assemblyTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Получаем СЗ Сборки по id Заявки
     * @param GetAssemblyTasksRequest $request
     * @return AnonymousResourceCollection|string
     */
    public function getAssemblyTasksByOrderId(GetAssemblyTasksRequest $request)
    {
        try {
            // __ Если передан ID — возвращаем одну конкретную задачу
            if (!$orderId = $request->validated('id')) {
                throw new Exception('Missing Order id from AssemblyTask');
            }

            // __ Получаем участки производства
            $sectors = $request->getSectors();

            $assemblyTasks = AssemblyTask::query()
                ->where('order_id', $orderId)
                ->with([
                    'order',
                    'order.client',
                    'order.orderType',
                    'statuses',
                    // __ Статистика
                    'sectorStatsTotal',
                    'lines.sectorStats',
                ])
                // __ Тут фильтруем по участкам и там же добавляем все связи
                ->sectors($sectors, [
                    'lines.orderLine',
                    'lines.orderLine.model',
                    'lines.orderLine.model.modelType',
                    'lines.orderLine.model.manufactureGroup',
                ])
                ->get();

            // !!! Отфильтровываем Чехол
            $assemblyTasks = AssemblyService::filterCovers($assemblyTasks);

            return AssemblyTaskResource::collection($assemblyTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }



    /**
     * ___ Добавляем СЗ для Сборки
     * @param Request $request
     * @return string
     */
    public function addAssemblyTasksByOrderId(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:orders,id'
            ]);

            AssemblyService::createAssemblyTaskFromOrderId($validated['id']);

            return EndPointStaticRequestAnswer::ok('СЗ успешно создано');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем СЗ для Сборки
     * @param Request $request
     * @return string
     */
    public function deleteAssemblyTasksByOrderId(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:orders,id'
            ]);

            DB::transaction(function () use ($validated) {
                // __ Меняем позиции СЗ в днях, где удаляем
                $deletedTasks = AssemblyTask::query()
                    ->select(['id', 'action_at'])
                    ->where('order_id', $validated['id'])
                    ->get();

                // __ Удаляем здесь
                AssemblyTask::query()
                    ->where('order_id', $validated['id'])
                    ->delete();

                foreach ($deletedTasks as $deletedTask) {
                    $tasksToUpdate = [];
                    $pos           = 1;
                    $existTasks    = AssemblyTask::query()
                        ->select(['id', 'action_at', 'position', 'change'])
                        ->where('change', AssemblyDay::CHANGE_1)
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

                    AssemblyService::bulkUpdateTasks($tasksToUpdate);
                }
            });

            return EndPointStaticRequestAnswer::ok('СЗ успешно удалено');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }



    /**
     * ___ Обновляем Комментарий Записи
     * @param Request $request
     * @return string
     */
    public function setAssemblyTaskLineSectorDescription(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'          => 'required|integer|exists:assembly_task_line_sectors,id',
                'description' => 'nullable|string',
            ]);

            $description = $validated['description'] ?? null;
            AssemblyTaskLineSector::query()
                ->where('id', $validated['id'])
                ->update(['description' => $description]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }



    /**
     * ___ Обновляем СЗ на Блоки
     * @param SyncAssemblyTasksRequest $request
     * @return mixed|string
     * @noinspection DuplicatedCode
     */
    public function updateAssemblyTasks(SyncAssemblyTasksRequest $request)
    {
        // !!! TODO: SyncAssemblyTasksRequest
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

                $a = 0;

                foreach ($diffs as $diff) {
                    $currentTaskId = null;  // __ Маяк созданного СЗ (Если флаг в lines - ADDED и в tasks - ADDED)
                    $updatedTaskId = null;  // __ Маяк обновляемого СЗ (Если флаг в lines - ADDED, но эти lines приходят из DELETED task и в tasks - UPDATED)

                    // --- 1. ОБРАБОТКА ЗАДАЧИ ---

                    switch ($diff['type']) {
                        case 'ADDED':

                            // __ Создаем новую задачу
                            $newTask = AssemblyTask::query()
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
                                AssemblyTaskStatus::ASSEMBLY_STATUS_CREATED_ID => [
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

                            // $task = AssemblyTask::query()->findOrFail($diff['taskId']);

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
                            AssemblyTask::destroy($diff['taskId']);
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
                                    $sourceLine = AssemblyTaskLine::query()
                                        ->with('sectors') // __ Подгружаем секторы исходной строки
                                        ->findOrFail($lineDiff['lineIdRef']);

                                    $newLine = $sourceLine->replicate();


                                    //$newLine = AssemblyTaskLine::query()
                                    //    ->findOrFail($lineDiff['lineIdRef'])
                                    //    ->replicate();

                                    // __ Ситуация, когда новые строки появились в новом СЗ
                                    // __ Связываем с новым СЗ
                                    if ($currentTaskId) {
                                        $newLine->assembly_task_id = $currentTaskId;
                                    }

                                    // __ Устанавливаем позицию в отрицательную зону
                                    // __ танцы "с бубнами" из-за ограничения пар ключей - устанавливаем уникальную позицию
                                    $newLine->position *= -1;
                                    $newLine->save();
                                    $newLine->position = $newLine->id * (-1);
                                    $newLine->save();

                                    // __ 🔗 Копируем все секторы из старой строки в новую!
                                    foreach ($sourceLine->sectors as $sector) {
                                        $newSector = $sector->replicate();
                                        $newSector->assembly_task_line_id = $newLine->id; // Привязываем к новому ID
                                        $newSector->save();
                                    }

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
                                        // 'assembly_task_id' => $updatedTaskId ?? null,
                                    ];

                                    // __ Собираем id линий для пересчета трудозатрат
                                    if (isset($lineDiff['amount'])) {
                                        $linesToRecalcTime[] = $lineDiff['lineId'];
                                    }

                                    break;

                                case 'DELETED':
                                    AssemblyTaskLine::destroy($lineDiff['lineId']);
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
                            if ($diff['taskChanges']['status']['new'] === AssemblyTaskStatus::ASSEMBLY_STATUS_RUNNING_ID) {
                                $setTask = AssemblyTask::query()->findOrFail($diff['taskId']);
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

                //// __ Пересчитываем трудозатраты при разделении количества
                //if (count($linesToRecalcTime) !== 0) {
                //    foreach ($linesToRecalcTime as $lineCalcId) {
                //        $assemblyTaskLine = AssemblyTaskLine::query()->with('assembly.assemblyCollection')->findOrFail($lineCalcId);
                //
                //        $assembly = $assemblyTaskLine->assembly;
                //        if (!$assembly) {
                //            throw new Exception('Missing Assembly with in AssemblyTaskLine with $id = ' . $lineCalcId);
                //        }
                //
                //        // __ Получаем так, потому что collection - поле в Assembly
                //        $collection = $assembly->getRelation('assemblyCollection');
                //
                //        $assemblyTaskLine->time = $collection->productivity !== 0.0
                //            ? ($assembly->length * $assembly->width / 100 / 100) * $assemblyTaskLine->amount / $collection->productivity
                //            : 0;
                //        $assemblyTaskLine->save();
                //    }
                //}

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
        $table = (new AssemblyTask)->getTable();

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
        $table = (new AssemblyTaskLine)->getTable();

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
                // if (isset($row['assembly_task_id'])) {
                //     $casesTaskId[] = "WHEN id = ? THEN ?";
                //     array_push($paramsTaskId, $row['id'], $row['assembly_task_id']);
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
            //     $setParts[]  = "assembly_task_id = CASE ".implode(' ', $casesTaskId)." ELSE assembly_task_id END";
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
    public function setAssemblyTaskComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'      => 'required|integer|exists:assembly_tasks,id',
                'comment' => 'present|nullable|string',
            ]);

            $assemblyTask = AssemblyTask::query()->find($validated['id']);
            if (!$assemblyTask) {
                throw new Exception('Missing assembly task with id: ' . $validated['id'] . '.');
            }

            $assemblyTask->comment = $validated['comment'];
            $assemblyTask->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Меняем Линию Сборки
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
                'data.*.id'   => 'required|integer|exists:assembly_task_lines,id',

                // __ Проверяем строку 'table' на соответствие конкретным значениям
                'data.*.line' => [
                    'required',
                    'string',
                    Rule::in([
                        Model::ASSEMBLY_LINE_LAMIT,
                        Model::ASSEMBLY_LINE_TABLE,
                    ]),
                ],
            ]);

            // __ Валидация с кастомными сообщениями
            //$validated = $request->validate([
            //    'data' => 'required|array|min:1',
            //    'data.*.id' => 'required|integer|exists:assembly_task_lines,id',
            //    'data.*.table' => ['required', 'string', Rule::in([AssemblyTaskLine::FIELD_TABLE_1, AssemblyTaskLine::FIELD_TABLE_2, AssemblyTaskLine::FIELD_TABLE_3])],
            //], [
            //    'data.required' => 'Массив данных обязателен для заполнения.',
            //    'data.min' => 'Массив данных не должен быть пустым.',
            //    'data.*.id.exists' => 'Выбранный ID задачи не существует в базе данных.',
            //    'data.*.table.in' => 'Поле table должно принимать значения: table_1, table_2 или table_3.',
            //]);

            $data = $validated['data'];
            DB::transaction(function () use ($data) {
                foreach ($data as $item) {
                    $line = AssemblyTaskLine::query()->find($item['id']);
                    if (!$line) {
                        throw new Exception('Missing assembly task line with id: ' . $item['id'] . '.');
                    }
                    $line->assembly_line = $item['line'];
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
            //    UPDATE assembly_task_lines AS c
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

