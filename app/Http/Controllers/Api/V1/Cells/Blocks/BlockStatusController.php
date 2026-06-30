<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Blocks\Statuses\BlockTaskStatusResource;
use App\Models\Manufacture\Cells\Block\BlockTask;
use App\Models\Manufacture\Cells\Block\BlockTaskStatus;
use App\Models\Manufacture\Cells\Block\BlockTaskStatusPivot;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

// use Illuminate\Http\Request;

class BlockStatusController extends Controller
{

    /**
     * ___ Возвращает список Статусов движения Заявки
     * @return AnonymousResourceCollection|string
     */
    public function getBlockTaskStatuses()
    {
        try {
            return BlockTaskStatusResource::collection(BlockTaskStatus::all());
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Обновляем цвет Типа Заявки
     * @param  Request  $request
     * @return string
     */
    public function patchBlockTaskStatusColor(Request $request)
    {
        try {
            $validated = $request->validate([
                'data'       => 'required|array',
                'data.color' => 'required|hex_color',
                'data.id'    => 'required|exists:block_task_statuses,id'
            ]);

            $validated = $validated['data'];

            BlockTaskStatus::query()->findOrFail($validated['id'])->update(['color' => $validated['color']]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // ___ Смена статусов Заявок с удалением старых
    public function setBlockTasksStatuses(Request $request)
    {
        try {
            $items = $request->all();
            $allStatuses = BlockTaskStatus::all()->pluck('id')->toArray();

            foreach ($items as $item) {
                if (!in_array($item['status'], $allStatuses)) {
                    continue;
                }

                $blockTask = BlockTask::query()->find($item['task']);

                if (!$blockTask) {
                    continue;
                }

                $blockTask->statuses()->attach($item['status'], [
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
    public function setBlockTasksStatusesWithDeleting(Request $request)
    {
        try {
            $items       = $request->all();
            $taskIds     = collect($items)->pluck('id')->toArray();
            $allStatuses = BlockTaskStatus::all()->keyBy('id');

            // __ 1. Собираем ID всех записей в pivot, которые нужно удалить при откате
            $pivotsToDelete     = [];
            $newRecordsToInsert = [];

            // __ Чтобы найти, что удалять, нам нужно посмотреть на ВСЮ историю присланных задач
            // __ Загружаем pivot с их позициями статусов
            $history = BlockTaskStatusPivot::query()
                ->whereIn('block_task_id', $taskIds)
                ->join('block_task_statuses', 'block_task_status_pivot.block_task_status_id', '=', 'block_task_statuses.id')
                ->select('block_task_status_pivot.*', 'block_task_statuses.position')
                ->get()
                ->groupBy('block_task_id');

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
                        if ($taskHistory->where('block_task_status_id', $newStatusId)->count() > 0) {
                            continue;
                        }
                    }

                    // __ Если статус не изменился — пропускаем
                    if ($newStatusId == $currentPivot->block_task_status_id) {
                        continue;
                    }
                }

                // __ Подготовка для НАКАТА.
                $newRecordsToInsert[] = [
                    'block_task_id'        => $taskId,
                    'block_task_status_id' => $newStatusId,
                    'set_at'                => now(),
                    'created_at'            => now(),
                ];
            }

            // __ 2. Исполнение в БД
            DB::transaction(function () use ($pivotsToDelete, $newRecordsToInsert) {
                // __ Удаляем всё одним махом
                if (!empty($pivotsToDelete)) {
                    BlockTaskStatusPivot::query()->whereIn('id', $pivotsToDelete)->delete();
                }

                // __ Вставляем всё одним махом
                if (!empty($newRecordsToInsert)) {
                    DB::table('block_task_status_pivot')->insert($newRecordsToInsert);
                }
            });

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
