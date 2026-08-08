<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacture\Assembly\GetAssemblyTasksRequest;
use App\Http\Resources\Manufacture\Cells\Assembly\Manage\AssemblyTaskResource;
use App\Models\Manufacture\Cells\Assembly\AssemblyTask;
use App\Services\DefaultsService;
use App\Services\Manufacture\AssemblyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

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
}
