<?php

namespace App\Http\Controllers\Api\V1\Plans;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Models\Plan\PlanLoad;
use App\Services\ClientsService;
use App\Services\OrdersService;
use App\Services\PlanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class PlanLoadsController extends Controller
{

    /**
     * ___ Обновляем план загрузок
     * @param Request $request
     * @return string|void
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
                    ->whereYear('period', Carbon::parse($load['unload_at'])->year)
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
                    $workload->load_at = PlanService::normalizeToCarbon($load['load_at']);
                    $workload->period = PlanService::getLoadPeriod($load['load_at']);
                    $workload->order_type_id = $orderType->id;
                    $workload->unload_at = $load['unload_at'] === '' ? null : PlanService::normalizeToCarbon($load['unload_at']);
                    $workload->amounts = $load['amounts'];
                    $workload->save();
                }



                $insertData = [
                    'client_id'     => $load['client_id'],
                    'order_no'      => $load['order_no'],
                    'amounts'       => $load['amounts'],
                    'active'        => true,
                    'order_type_id' => $orderType->id,
                    'period'        => PlanService::getLoadPeriod($load['load_at']),
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
                    throw new Exception('Error while creating plan load');
                }
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
