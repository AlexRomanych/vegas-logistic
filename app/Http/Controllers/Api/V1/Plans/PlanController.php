<?php

namespace App\Http\Controllers\Api\V1\Plans;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Plans\Loads\PlanLoadsResource;
use App\Http\Resources\Plans\Loads\PlanResource;
use App\Models\BusinessProcesses\BusinessProcess;
use App\Models\Plan\Plan;
use App\Models\Plan\PlanLoad;
use App\Services\BusinessProcessesService;
use App\Services\ClientsService;
use App\Services\DefaultsService;
use App\Services\OrdersService;
use App\Services\PlanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlanController extends Controller
{

    /**
     * ___ Получаем план Узла Бизнес-процесса за период
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getPlanBusinessProcessNode(Request $request)
    {
        try {
            $validated = $request->validate([
                'process'      => 'required|numeric|exists:business_processes,id',
                'node'         => 'required|numeric|exists:business_process_nodes,id',
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodPlan();
                $start = Carbon::parse($period->getStart());
                $end = Carbon::parse($period->getEnd());
            }

            // __ Получаем смещение в днях для конкретного узла
            $offset = BusinessProcessesService::getDateOffsetForOrderMovingProcessByNodeIdAndClientId($validated['node']);

            $planLoadsTableName = (new PlanLoad())->getTable(); // Получаем имя таблицы референсного плана - Плана Загрузок
            $planLoads = Plan::query()
                ->from($planLoadsTableName . ' as p') // 👈 Указываем таблицу и, по желанию, алиас (p)
                // ->from('plan_loads as p') // 👈 Указываем таблицу и, по желанию, алиас (p)
                ->whereDate('p.load_at', '>=', $start)
                ->whereDate('p.load_at', '<=', $end)
                ->with(['client', 'orderType'])
                ->orderBy('p.load_at') // 👈 Необходимо использовать алиас для полей
                ->get();


            // $planLoads = Plan::query()
            //     ->whereDate('load_at', '>=', $start)     // Используем такую конструкцию, потому что
            //     ->whereDate('load_at', '<=', $end)       // ->whereBetween() не включает периоды
            //     ->with(['client', 'orderType'])
            //     ->orderBy('load_at')
            //     ->get();



            // __ Добавляем новое поле 'action_at' к каждому элементу коллекции
            $planLoads->each(function ($item) use ($offset) {
                $loadAtCarbon = Carbon::parse($item->load_at);
                $actionCarbon = $loadAtCarbon->copy()->addDays($offset);
                $item->action_at = $actionCarbon;
                // $item->action_at = $actionCarbon->timestamp;
            });

            return PlanResource::collection($planLoads);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
