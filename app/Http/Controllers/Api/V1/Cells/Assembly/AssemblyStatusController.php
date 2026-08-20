<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Assembly\Statuses\AssemblyTaskStatusResource;
use App\Models\Manufacture\Cells\Assembly\AssemblyTask;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskStatus;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskStatusPivot;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

// use Illuminate\Http\Request;

class AssemblyStatusController extends Controller
{

    /**
     * ___ Возвращает список Статусов движения Заявки
     * @return AnonymousResourceCollection|string
     */
    public function getAssemblyTaskStatuses()
    {
        try {
            return AssemblyTaskStatusResource::collection(AssemblyTaskStatus::all());
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Обновляем цвет Типа Заявки
     * @param  Request  $request
     * @return string
     */
    public function patchAssemblyTaskStatusColor(Request $request)
    {
        try {
            $validated = $request->validate([
                'data'       => 'required|array',
                'data.color' => 'required|hex_color',
                'data.id'    => 'required|exists:assembly_task_statuses,id'
            ]);

            $validated = $validated['data'];

            AssemblyTaskStatus::query()->findOrFail($validated['id'])->update(['color' => $validated['color']]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Смена статусов Заявок с добавлением
     * @param Request $request
     * @return string
     */
    public function setAssemblyTasksStatuses(Request $request)
    {
        try {
            $items = $request->all();
            $allStatuses = AssemblyTaskStatus::all()->pluck('id')->toArray();

            foreach ($items as $item) {
                if (!in_array($item['status'], $allStatuses)) {
                    continue;
                }

                /** @var AssemblyTask $assemblyTask */
                $assemblyTask = AssemblyTask::query()->find($item['task']);

                if (!$assemblyTask) {
                    continue;
                }
                $assemblyTask->statuses()->attach($item['status'], [
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
    public function setAssemblyTasksStatusesWithDeleting(Request $request)
    {
        try {
            $items       = $request->all();
            $taskIds     = collect($items)->pluck('id')->toArray();
            $allStatuses = AssemblyTaskStatus::all()->keyBy('id');

            // __ 1. Собираем ID всех записей в pivot, которые нужно удалить при откате
            $pivotsToDelete     = [];
            $newRecordsToInsert = [];

            // __ Чтобы найти, что удалять, нам нужно посмотреть на ВСЮ историю присланных задач
            // __ Загружаем pivot с их позициями статусов
            $history = AssemblyTaskStatusPivot::query()
                ->whereIn('assembly_task_id', $taskIds)
                ->join('assembly_task_statuses', 'assembly_task_status_pivot.assembly_task_status_id', '=', 'assembly_task_statuses.id')
                ->select('assembly_task_status_pivot.*', 'assembly_task_statuses.position')
                ->get()
                ->groupBy('assembly_task_id');

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
                        if ($taskHistory->where('assembly_task_status_id', $newStatusId)->count() > 0) {
                            continue;
                        }
                    }

                    // __ Если статус не изменился — пропускаем
                    if ($newStatusId == $currentPivot->assembly_task_status_id) {
                        continue;
                    }
                }

                // __ Подготовка для НАКАТА.
                $newRecordsToInsert[] = [
                    'assembly_task_id'        => $taskId,
                    'assembly_task_status_id' => $newStatusId,
                    'set_at'                => now(),
                    'created_at'            => now(),
                ];
            }

            // __ 2. Исполнение в БД
            DB::transaction(function () use ($pivotsToDelete, $newRecordsToInsert) {
                // __ Удаляем всё одним махом
                if (!empty($pivotsToDelete)) {
                    AssemblyTaskStatusPivot::query()->whereIn('id', $pivotsToDelete)->delete();
                }

                // __ Вставляем всё одним махом
                if (!empty($newRecordsToInsert)) {
                    DB::table('assembly_task_status_pivot')->insert($newRecordsToInsert);
                }
            });

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
