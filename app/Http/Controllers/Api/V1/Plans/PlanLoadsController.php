<?php

namespace App\Http\Controllers\Api\V1\Plans;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Plans\Loads\PlanLoadsResource;
use App\Models\Client;
use App\Models\Order\OrderLine;
use App\Models\Plan\PlanLoad;
use App\Services\BusinessProcessesService;
use App\Services\ClientsService;
use App\Services\DefaultsService;
use App\Services\Manufacture\SewingService;
use App\Services\ModelsService;
use App\Services\OrdersService;
use App\Services\Plan\PlanLoadsService;
use App\Services\PlanService;
use App\Services\SizeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

class PlanLoadsController extends Controller
{

    // ___ Проверяем план загрузок на валидность
    public function validateLoads(Request $request)
    {
        try {
            $data = $request->validate(['data' => 'required|json']);

            $planLoads = json_decode($data['data'], true);

            $validatedPlanLoads = PlanLoadsService::validatePlanLoads($planLoads);

            return ['data' => $validatedPlanLoads];
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем или загружаем план загрузок
     * @param Request $request
     * @return string
     * @throws Throwable
     */
    public function uploadLoads(Request $request)
    {
        // try {
        // $all = $request->all();

        $validated = $request->validate([
            'data' => 'required|json'
        ]);

        // DB::table('orders')->truncate();

        $planLoads = json_decode($validated['data'], true);

        // Дополнительная проверка структуры JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON structure');
        }

        $loadsUpdated = []; // Массив обновленных отгрузок, а не созданных для ответа

        foreach ($planLoads as $planLoad) {

            // __ Проверяем действие
            if ($planLoad[VALIDATE_FIELD][ACTION_FIELD] === ACTION_NONE ||
                $planLoad[VALIDATE_FIELD][ACTION_FIELD] === ACTION_ORDER_IGNORE
            ) {
                continue;
            }
            if ($planLoad[VALIDATE_FIELD][ACTION_FIELD] !== ACTION_ORDER_ADD) {
                continue;
            }

            // __ Проверяем наличие клиента на всякий случай
            // __ Пробуем получить клиента по ID
            $client = null;
            if ($planLoad['client_id'] !== 0) {
                $client = Client::query()->find($planLoad['client_id']);
            }
            if (!$client) {
                throw new Exception('Client with id = ' . $planLoad['client_id'] . ' not found');
            }

            // __ Получаем Тип изделий в Заявках
            $elementsType = OrdersService::getElementsTypeFromRender($planLoad['elements_type']);

            // __ Пробуем найти Заявку в БД, причем с учетом периода, если не нашли - пробуем соседние периоды
            // __ Вероятность, что Заявка у такого клиента с таким номером попадет другой период (+- год) практически нулевая
            // __ Перебираем периоды, чтобы наверняка исключить косяки с датами из 1С
            $existOrder = OrdersService::getOrderByClientIdOrderNoElementsTypeLoadAt(
                $client->id,
                $planLoad['order_no'],
                $elementsType,
                $planLoad['load_at']
            );

            // __ Проверка на всякий случай
            // __ Если не нашли Заявку с таким номером в БД и она прогнозная - создаем ее
            if (!$existOrder && $planLoad['amounts']['averages'] !== 0) {

                $averageModel = ModelsService::createAverageModel($client->id, $elementsType);
                if (!$averageModel) {
                    throw new Exception('Error while creating average model with Client id = ' . $planLoad['client_id']);

                }
            }

            // __ Получаем тип заявки по номеру (гар. рем, серийная и т.д.)
            $orderType = OrdersService::getOrderTypeByIndex(AVERAGE_TYPE_INDEX); // Прогнозная Заявка
            // $orderType = OrdersService::getOrderTypeByOrderNoAndClientId($planLoad['order_no'], $client->id);

            $createdOrder = PlanLoad::query()->create([
                'client_id'         => $client->id,
                'order_type_id'     => $orderType->id,
                'plan_load_id'      => 0, // TODO: Повесить Observer и создать плановую загрузку, пока не будем управлять сущностью
                'order_no_num'      => parseNumericValue($planLoad['order_no']),
                'order_no_str'      => $planLoad['order_no'],
                'order_no_origin'   => $planLoad['order_no'],
                'no_1c'             => '',   // Используем на фронте
                'is_forecast'       => true, // Прогнозная Заявка
                'order_period'      => PlanService::getOrderPeriod($planLoad['load_at']),
                'elements_type'     => $elementsType,
                'elements_type_ref' => OrdersService::getOrderElementsTypeReference($elementsType),

                // __ Вставляем именно массивом, без преобразования в json
                'amounts'           => [
                    'mattresses' => 0,
                    'up_covers'  => 0,
                    'averages'   => $planLoad['amounts']['averages'],
                    'covers'     => 0,
                    'children'   => 0,
                    'formats'    => 0,
                    'unknowns'   => 0,
                    'totals'     => $planLoad['amounts']['averages'],
                ],

                'load_at'   => PlanService::normalizeToCarbon($planLoad['load_at']),
                'unload_at' => $planLoad['unload_at'] === '' ? null : PlanService::normalizeToCarbon($planLoad['unload_at']),


                // 'responsible'        => null,
                // 'manager_load_date'  => null,
                // 'manager_start'      => null,
                // 'manager_end'        => null,
                // 'design_start'       => null,
                // 'design_end'         => null,
                // 'no_1c'              => null,
                // 'code_1c'            => null,
                // 'base_order_code_1c' => null,
                // 'comment_1c'         => null,
                // 'client_code_1c'     => null,
                // 'client_name_1c'     => null,
                // 'service'            => null,

                // 'status'             => $order[''],

                // 'shown'              => $order[''],
                // 'stat_include'       => $order[''],
                // 'service_ext'        => $order[''],
                // 'extended_meta'      => $order[''],
                // 'description'        => $order[''],
                // 'comment'            => $order[''],
                // 'note'               => $order[''],
                // 'meta'               => $order[''],
                // 'history'            => $order[''],
                // 'meta_ext'           => $order[''],
                // 'active'             => $order[''],
            ]);

            if (!$createdOrder) {
                throw new Exception('Error while creating Average Order with Client id = ' . $planLoad['client_id'] . ' is failed');
            }

            // __ Вставляем содержимое Прогнозной Заявки
            // __ Получаем размеры
            $AVERAGE_SIZE_STR = '0x0x0';
            $dims = SizeService::getDimensions($AVERAGE_SIZE_STR);

            /** @noinspection PhpUndefinedVariableInspection */
            $createLine = OrderLine::query()->create(
                [
                    'order_id'      => $createdOrder->id,
                    'size'          => $AVERAGE_SIZE_STR,
                    'width'         => $dims->getWidth(),
                    'length'        => $dims->getLength(),
                    'height'        => $dims->getHeight(),
                    'model_name'    => $averageModel->name,
                    'model_code_1c' => $averageModel->code_1c,
                    'amount'        => $planLoad['amounts']['averages'],
                    // 'textile'       => $orderLine['t'],
                    // 'composition'   => $orderLine['d'],
                    // 'describe_1'    => $orderLine['d1'],
                    // 'describe_2'    => $orderLine['d2'],
                    // 'describe_3'    => $orderLine['d3'],
                    // 'active'        => $orderLine[''],
                    // 'status'        => $orderLine[''],
                    // 'description'   => $orderLine[''],
                    // 'comment'       => $orderLine[''],
                    // 'note'          => $orderLine[''],
                    // 'meta'          => $orderLine[''],
                ]
            );

            if (!$createLine) {
                throw new Exception('Error while creating Average Order Line with Client id = ' . $planLoad['client_id'] . ' is failed');
            }


            // __ Тут начинаем формировать СЗ на различные участки

            // __ Создаем СЗ на Пошив
            $sewingTask = SewingService::createSewingTaskFromOrderId($createdOrder->id);
            if (!$sewingTask) {
                throw new Exception('Error while creating Sewing Task with Client id = ' . $planLoad['client_id']);
            }




        }

        // return EndPointStaticRequestAnswer::ok();
        // } catch (Exception $e) {
        //     return EndPointStaticRequestAnswer::fail($e);
        // }
    }


    public function uploadLoads_Old(Request $request)
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
