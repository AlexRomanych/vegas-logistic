<?php

namespace App\Http\Controllers\Api\V1\Cells\Cutting;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Cutting\Statuses\CuttingTaskStatusResource;
use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskStatus;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskStatusPivot;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

// use Illuminate\Http\Request;

class CellCuttingStatusController extends Controller
{

    /**
     * ___ Возвращает список Статусов движения Заявки
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingTaskStatuses()
    {
        try {
            return CuttingTaskStatusResource::collection(CuttingTaskStatus::all());
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Обновляем цвет Типа Заявки
     * @param  Request  $request
     * @return string
     */
    public function patchCuttingTaskStatusColor(Request $request)
    {
        try {
            $validated = $request->validate([
                'data'       => 'required|array',
                'data.color' => 'required|hex_color',
                'data.id'    => 'required|exists:cutting_task_statuses,id'
            ]);

            $validated = $validated['data'];

            CuttingTaskStatus::query()->findOrFail($validated['id'])->update(['color' => $validated['color']]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // ___ Смена статусов Заявок с удалением старых
    public function setCuttingTasksStatuses(Request $request)
    {
        try {
            $items = $request->all();
            $allStatuses = CuttingTaskStatus::all()->pluck('id')->toArray();

            foreach ($items as $item) {
                if (!in_array($item['status'], $allStatuses)) {
                    continue;
                }

                $cuttingTask = CuttingTask::query()->find($item['task']);

                if (!$cuttingTask) {
                    continue;
                }

                $cuttingTask->statuses()->attach($item['status'], [
                    'set_at'     => now(),
                    'created_at' => now(),
                    'created_by' => auth()->id(),
                ]);
            }
            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // ___ Смена статусов Заявок с удалением старых
    public function setCuttingTasksStatusesWithDeleting(Request $request)
    {
        try {
            $items       = $request->all();
            $taskIds     = collect($items)->pluck('id')->toArray();
            $allStatuses = CuttingTaskStatus::all()->keyBy('id');

            // __ 1. Собираем ID всех записей в pivot, которые нужно удалить при откате
            $pivotsToDelete     = [];
            $newRecordsToInsert = [];

            // __ Чтобы найти, что удалять, нам нужно посмотреть на ВСЮ историю присланных задач
            // __ Загружаем pivot с их позициями статусов
            $history = CuttingTaskStatusPivot::query()
                ->whereIn('cutting_task_id', $taskIds)
                ->join('cutting_task_statuses', 'cutting_task_status_pivot.cutting_task_status_id', '=', 'cutting_task_statuses.id')
                ->select('cutting_task_status_pivot.*', 'cutting_task_statuses.position')
                ->get()
                ->groupBy('cutting_task_id');

            foreach ($items as $item) {
                $taskId      = $item['id'];
                $newStatusId = $item['status'];
                $newPos      = $allStatuses[$newStatusId]->position;

                $taskHistory  = $history->get($taskId) ?? collect();
                $currentPivot = $taskHistory->sortByDesc('id')->first();

                if ($currentPivot) {
                    // __ Если это ОТКАТ: собираем все ID pivot, где позиция больше новой
                    if ($newPos < $currentPivot->position) {
                        $ids            = $taskHistory->where('position', '>', $newPos)->pluck('id')->toArray();
                        $pivotsToDelete = array_merge($pivotsToDelete, $ids);

                        // __ Если целевой статус уже есть в истории, новый INSERT не нужен
                        if ($taskHistory->where('cutting_task_status_id', $newStatusId)->count() > 0) {
                            continue;
                        }
                    }

                    // __ Если статус не изменился — пропускаем
                    if ($newStatusId == $currentPivot->cutting_task_status_id) {
                        continue;
                    }
                }

                // __ Подготовка для НАКАТА.
                $newRecordsToInsert[] = [
                    'cutting_task_id'        => $taskId,
                    'cutting_task_status_id' => $newStatusId,
                    'set_at'                => now(),
                    'created_at'            => now(),
                ];
            }

            // __ 2. Исполнение в БД
            DB::transaction(function () use ($pivotsToDelete, $newRecordsToInsert) {
                // __ Удаляем всё одним махом
                if (!empty($pivotsToDelete)) {
                    CuttingTaskStatusPivot::query()->whereIn('id', $pivotsToDelete)->delete();
                }

                // __ Вставляем всё одним махом
                if (!empty($newRecordsToInsert)) {
                    DB::table('cutting_task_status_pivot')->insert($newRecordsToInsert);
                }
            });

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
