<?php

namespace App\Http\Controllers\Api\V1\Cells\Sewing;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Sewing\Statuses\SewingTaskStatusResource;
use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Models\Manufacture\Cells\Sewing\SewingTaskStatus;
use App\Models\Manufacture\Cells\Sewing\SewingTaskStatusPivot;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

// use Illuminate\Http\Request;

class CellSewingStatusController extends Controller
{

    /**
     * ___ Возвращает список Статусов движения Заявки
     * @return AnonymousResourceCollection|string
     */
    public function getSewingTaskStatuses()
    {
        try {
            return SewingTaskStatusResource::collection(SewingTaskStatus::all());
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Обновляем цвет Типа Заявки
     * @param  Request  $request
     * @return string
     */
    public function patchSewingTaskStatusColor(Request $request)
    {
        try {
            $validated = $request->validate([
                'data'       => 'required|array',
                'data.color' => 'required|hex_color',
                'data.id'    => 'required|exists:sewing_task_statuses,id'
            ]);

            $validated = $validated['data'];

            SewingTaskStatus::query()->findOrFail($validated['id'])->update(['color' => $validated['color']]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // ___ Смена статусов Заявок с удалением старых
    public function setSewingTasksStatuses(Request $request)
    {
        try {
            $items = $request->all();
            $allStatuses = SewingTaskStatus::all()->pluck('id')->toArray();

            foreach ($items as $item) {
                if (!in_array($item['status'], $allStatuses)) {
                    continue;
                }

                $sewingTask = SewingTask::query()->find($item['task']);

                if (!$sewingTask) {
                    continue;
                }

                $sewingTask->statuses()->attach($item['status'], [
                    'set_at'     => now(),
                    'created_at' => now(),
                ]);
            }
            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // ___ Смена статусов Заявок с удалением старых
    public function setSewingTasksStatusesWithDeleting(Request $request)
    {
        try {
            $items       = $request->all();
            $taskIds     = collect($items)->pluck('id')->toArray();
            $allStatuses = SewingTaskStatus::all()->keyBy('id');

            // __ 1. Собираем ID всех записей в pivot, которые нужно удалить при откате
            $pivotsToDelete     = [];
            $newRecordsToInsert = [];

            // __ Чтобы найти, что удалять, нам нужно посмотреть на ВСЮ историю присланных задач
            // __ Загружаем pivot с их позициями статусов
            $history = SewingTaskStatusPivot::query()
                ->whereIn('sewing_task_id', $taskIds)
                ->join('sewing_task_statuses', 'sewing_task_status_pivot.sewing_task_status_id', '=', 'sewing_task_statuses.id')
                ->select('sewing_task_status_pivot.*', 'sewing_task_statuses.position')
                ->get()
                ->groupBy('sewing_task_id');

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
                        if ($taskHistory->where('sewing_task_status_id', $newStatusId)->count() > 0) {
                            continue;
                        }
                    }

                    // __ Если статус не изменился — пропускаем
                    if ($newStatusId == $currentPivot->sewing_task_status_id) {
                        continue;
                    }
                }

                // __ Подготовка для НАКАТА.
                $newRecordsToInsert[] = [
                    'sewing_task_id'        => $taskId,
                    'sewing_task_status_id' => $newStatusId,
                    'set_at'                => now(),
                    'created_at'            => now(),
                ];
            }

            // __ 2. Исполнение в БД
            DB::transaction(function () use ($pivotsToDelete, $newRecordsToInsert) {
                // __ Удаляем всё одним махом
                if (!empty($pivotsToDelete)) {
                    SewingTaskStatusPivot::query()->whereIn('id', $pivotsToDelete)->delete();
                }

                // __ Вставляем всё одним махом
                if (!empty($newRecordsToInsert)) {
                    DB::table('sewing_task_status_pivot')->insert($newRecordsToInsert);
                }
            });

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
