<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacture\Assembly\GetAssemblyTasksRequest;
use App\Http\Resources\Manufacture\Cells\Assembly\Manage\AssemblyTaskResource;
use App\Models\Manufacture\Cells\Assembly\AssemblyDay;
use App\Models\Manufacture\Cells\Assembly\AssemblyTask;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskLineSector;
use App\Services\DefaultsService;
use App\Services\Manufacture\AssemblyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
                ])
                // __ Тут фильтруем по участкам и там же добавляем все связи
                ->sectors($sectors, [
                    'lines.orderLine',
                    'lines.orderLine.model',
                    'lines.orderLine.model.manufactureGroup',
                ])
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
                ])
                // __ Тут фильтруем по участкам и там же добавляем все связи
                ->sectors($sectors, [
                    'lines.orderLine',
                    'lines.orderLine.model',
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




}

