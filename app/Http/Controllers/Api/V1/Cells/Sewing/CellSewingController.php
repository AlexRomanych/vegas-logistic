<?php

namespace App\Http\Controllers\Api\V1\Cells\Sewing;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacture\Sewing\Sync\SyncSewingTasksRequest;
use App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage\SewingTaskResource;
use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Models\Manufacture\Cells\Sewing\SewingTaskLine;
use App\Models\Manufacture\Cells\Sewing\SewingTaskStatus;
use App\Services\DefaultsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CellSewingController extends Controller
{

    // ___ Получаем СЗ на Пошив
    public function getSewingTasks(Request $request)
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

            $sewingTasks = SewingTask::query()
                ->whereBetween('action_at', [
                    $start->startOfDay(),
                    $end->endOfDay()
                ])
                // ->whereDate('action_at', '>=', $start)     // Используем такую конструкцию, потому что
                // ->whereDate('action_at', '<=', $end)       // ->whereBetween() не включает периоды
                ->with([
                    'order.client',
                    'order.orderType',
                    'sewingLines.orderLine.model.cover',
                    'sewingLines.orderLine.model.base',
                    'statuses',
                ])
                // ->with(['sewingLines', 'sewingLines.orderLine','sewingLines.orderLine.model','sewingLines.orderLine.model.cover', 'statuses'])
                ->orderBy('action_at')
                ->get();


            return SewingTaskResource::collection($sewingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // ___ Обновляем СЗ на Пошив
    public function updateSewingTasks(SyncSewingTasksRequest $request)
    {
        // !!! TODO: SyncSewingTasksRequest
        try {
            $idMap = []; // __ Для возврата соответствия temp_id => real_id

            return DB::transaction(function () use ($request, &$idMap) {


                $tasksToUpdate = [];

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
                            $newTask = SewingTask::query()
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
                                SewingTaskStatus::SEWING_STATUS_CREATED_ID => [
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

                            // $task = SewingTask::query()->findOrFail($diff['taskId']);

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
                            SewingTask::destroy($diff['taskId']);
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

                                    $newLine = SewingTaskLine::query()
                                        ->findOrFail($lineDiff['lineIdRef'])
                                        ->replicate();

                                    // __ Ситуация, когда новые строки появились в новом СЗ
                                    // __ Связываем с новым СЗ
                                    if ($currentTaskId) {
                                        $newLine->sewing_task_id = $currentTaskId;
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
                                    break;

                                case 'UPDATED':

                                    $linesToUpdate[] = [
                                        'id'       => $lineDiff['lineId'],
                                        'amount'   => $lineDiff['amount']['new'] ?? null,
                                        'position' => $lineDiff['position']['new'] ?? null,
                                        // 'sewing_task_id' => $updatedTaskId ?? null,
                                    ];
                                    break;

                                case 'DELETED':
                                    SewingTaskLine::destroy($lineDiff['lineId']);
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

                // TODO: Разобраться с ответом
                return response()->json([
                    'status' => 'success',
                    'idMap'  => $idMap
                ]);
            });

        } catch (Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Массовое обновление СЗ
     * @param  array  $rows
     * @return void
     * @throws Throwable
     */
    private function bulkUpdateTasks(array $rows)
    {
        // __ Получаем имя таблицы
        $table = (new SewingTask)->getTable();

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


    /**
     * ___ Массовое обновление Записей через один запрос (Raw SQL Case)
     * @param  array  $rows
     * @return void
     * @throws Throwable
     */
    private function bulkUpdateLines(array $rows)
    {
        // __ Получаем имя таблицы
        $table = (new SewingTaskLine)->getTable();

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
                // if (isset($row['sewing_task_id'])) {
                //     $casesTaskId[] = "WHEN id = ? THEN ?";
                //     array_push($paramsTaskId, $row['id'], $row['sewing_task_id']);
                // }
            }

            $setParts    = [];
            $finalParams = [];

            if (!empty($casesAmount)) {
                $setParts[]  = "amount = CASE ".implode(' ', $casesAmount)." ELSE amount END";
                $finalParams = array_merge($finalParams, $paramsAmount);
            }

            if (!empty($casesPosition)) {
                $setParts[]  = "position = CASE ".implode(' ', $casesPosition)." ELSE position END";
                $finalParams = array_merge($finalParams, $paramsPosition);
            }

            // if (!empty($casesTaskId)) {
            //     $setParts[]  = "sewing_task_id = CASE ".implode(' ', $casesTaskId)." ELSE sewing_task_id END";
            //     $finalParams = array_merge($finalParams, $paramsTaskId);
            // }

            if (empty($setParts)) {
                return;
            }

            $wherePlaceholders = implode(',', array_fill(0, count($allIds), '?'));
            $sql               = "UPDATE {$table} SET ".implode(', ', $setParts)." WHERE id IN ({$wherePlaceholders})";

            // __ Соединяем параметры: параметры CASE1 + параметры CASE2 + параметры WHERE
            DB::update($sql, array_merge($finalParams, $allIds));
        });

    }



    /**
     * ___ Устанавливает комментарий к Сменному Заданию
     * @param  Request  $request
     * @return string
     */
    public function setSewingTaskComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'      => 'required|integer|exists:sewing_tasks,id',
                'comment' => 'present|nullable|string',
            ]);

            $sewingTask = SewingTask::query()->find($validated['id']);
            if (!$sewingTask) {
                throw new Exception('Missing sewing task with id: '.$validated['id'].'.');
            }

            $sewingTask->comment = $validated['comment'];
            $sewingTask->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }




    /**
     * Возвращает массив уникальных id заказов
     * @param  mixed  $data
     * @return array
     */
    private function getUniqueOrdersIds(mixed $data): array
    {
        $result = [];

        if (is_null($data) || is_bool($data)) {
            return $result;
        }

        foreach ($data as $item) {
            $result[] = $item['line']['order_id'];
        }


        return array_unique($result);
    }

}
