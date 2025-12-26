<?php

namespace App\Http\Controllers\Api\V1\Plans;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Plans\Loads\PlanLoadsResource;
use App\Models\Plan\PlanLoad;
use App\Services\ClientsService;
use App\Services\DefaultsService;
use App\Services\OrdersService;
use App\Services\PlanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlanLoadsController extends Controller
{

    /**
     * ___ Обновляем план загрузок
     * @param Request $request
     * @return string
     */
    public function uploadLoads(Request $request)
    {
        try {
            // $all = $request->all();

            $validated = $request->validate([
                'data' => 'required|json'
            ]);

            $loads = json_decode($validated['data'], true);

            // Дополнительная проверка структуры JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON structure');
            }

            $loadsUpdated = []; // Массив обновленных отгрузок, а не созданных для ответа

            foreach ($loads as $load) {

                // Проверка существования клиента
                $client = ClientsService::getClientById($load['client_id']);
                if (!$client) {
                    throw new Exception('Client with id = ' . $load['client_id'] . ' not found');
                }

                // Проверка существования заявки в плане загрузок
                $existLoads = PlanLoad::query()
                    ->where('client_id', $load['client_id'])
                    ->where('order_no', $load['order_no'])
                    ->whereYear('period', Carbon::parse($load['load_at'])->year)
                    ->get(); // Тут нарочно используем get() вместо first(), чтобы выявить коллизии (две заявки одного клиента в одном периоде)

                if ($existLoads->count() > 1) {
                    throw new Exception('Loads collisions detected');
                }

                // if (PlanService::isLoadsCollisionsPresent($existLoads)) {
                //     throw new \Exception('Loads collisions detected');
                // }

                // __ Если коллизий нет, и найдена отгрузка, то обновляем ее
                $orderType = OrdersService::getOrderTypeByOrderNoAndClientId($load['order_no']); // Получаем тип заявки по номеру

                if ($existLoads->count() == 1) {
                    $workload = $existLoads->first();

                    $loadAtMemory = $workload->load_at; // Запоминаем дату загрузки, чтобы потом сравнить с новой

                    $workload->load_at = PlanService::normalizeToCarbon($load['load_at']);
                    $workload->period = PlanService::getOrderPeriod($load['load_at']);
                    $workload->order_type_id = $orderType->id;
                    $workload->unload_at = $load['unload_at'] === '' ? null : PlanService::normalizeToCarbon($load['unload_at']);
                    $workload->amounts = $load['amounts'];
                    $workload->save();

                    $oldDate = Carbon::parse($loadAtMemory);
                    $newDate = Carbon::parse($workload->load_at);

                    // TODO: Доделать возврат обновленных отгрузок, для того, чтобы понимать, что с ними делать:
                    // TODO: обновить ли зависимые планы (швейки, закроя, сборки и т.д.), какие именно не нужно и т.д.
                    if (!$newDate->isSameDay($oldDate)) {    // Если дата не изменилась, то ничего не делаем
                        $loadsUpdated[] = $workload;
                    }

                } else { // __ Иначе создаем новую отгрузку

                    $insertData = [
                        'client_id'     => $load['client_id'],
                        'order_no'      => $load['order_no'],
                        'amounts'       => $load['amounts'],
                        'active'        => true,
                        'order_type_id' => $orderType->id,
                        'period'        => PlanService::getOrderPeriod($load['load_at']),
                        'load_at'       => PlanService::normalizeToCarbon($load['load_at']),
                        'unload_at'     => $load['unload_at'] === '' ? null : PlanService::normalizeToCarbon($load['unload_at']),

                        // 'status' => $load['status'],
                        // 'description' => $load['description'],
                        // 'comment' => $load['comment'],
                        // 'note' => $load['note'],
                        // 'meta' => $load['meta'],
                        // 'history' => $load['history'],
                        // 'extended_meta' => $load['extended_meta'],
                    ];

                    $planLoad = PlanLoad::query()->create($insertData);
                    if (!$planLoad) {
                        throw new Exception('Error while uploading plan loads');
                    }
                }
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем план загрузок
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getPlanLoads(Request $request)
    {
        try {
            $validated = $request->validate([
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodPlanLoads();
                $start = Carbon::parse($period->getStart());
                $end = Carbon::parse($period->getEnd());
            }

            $planLoads = PlanLoad::query()
                ->whereDate('load_at', '>=', $start)     // Используем такую конструкцию, потому что
                ->whereDate('load_at', '<=', $end)       // ->whereBetween() не включает периоды
                ->with(['client', 'orderType'])
                ->orderBy('load_at')
                ->get();

            return PlanLoadsResource::collection($planLoads);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // /**
    //  * ___ Получаем план Узла Бизнес-процесса за период
    //  * @param Request $request
    //  * @return AnonymousResourceCollection|string
    //  */
    // public function getPlanBusinessProcessNode(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'process'      => 'required|numeric|exists:business_processes,id',
    //             'node'         => 'required|numeric|exists:business_process_nodes,id',
    //             'period'       => 'nullable|array',
    //             'period.start' => 'required_if:period,*,!null|date',        // условная валидация
    //             'period.end'   => 'required_if:period,*,!null|date',
    //         ]);
    //
    //         /** @noinspection DuplicatedCode */
    //         if (isset($validated['period'])) {
    //             $start = Carbon::parse($validated['period']['start']);
    //             $end = Carbon::parse($validated['period']['end']);
    //         } else {
    //             $period = DefaultsService::getDefaultPeriodPlan();
    //             $start = Carbon::parse($period->getStart());
    //             $end = Carbon::parse($period->getEnd());
    //         }
    //
    //         $planLoads = PlanLoad::query()
    //             ->whereDate('load_at', '>=', $start)     // Используем такую конструкцию, потому что
    //             ->whereDate('load_at', '<=', $end)       // ->whereBetween() не включает периоды
    //             ->with(['client', 'orderType'])
    //             ->orderBy('load_at')
    //             ->get();
    //
    //
    //
    //
    //         return PlanLoadsResource::collection($planLoads);
    //     } catch (Exception $e) {
    //         return EndPointStaticRequestAnswer::fail($e);
    //     }
    // }


    /**
     * ___ Получаем период по умолчанию для плана загрузок
     * @return string
     */
    public function getPlanLoadsDefaultPeriod()
    {
        try {
            $period = DefaultsService::getDefaultPeriodPlanLoads();
            return DefaultsService::getDefaultPeriodPlanLoads()->toJson();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
