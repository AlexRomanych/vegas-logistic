<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Classes\EndPointStaticRequestAnswer;
use App\Enums\ElementTypes;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderTypes\OrderTypeResource;
use App\Http\Resources\Order\Render\OrderRenderResource;
use App\Models\Client;
use App\Models\Order\Order;
use App\Models\Order\OrderLine;

// use App\Models\Plan\PlanLoad;
use App\Models\Order\OrderStatus;
use App\Models\Order\OrderType;
use App\Services\Manufacture\SewingService;
use App\Services\ModelsService;
use App\Services\OrdersService;
use App\Services\PlanService;
use App\Services\SizeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderController extends Controller
{

    // Отдаем заказы за период
    public function getOrders(Request $request)
    {

        // $orders = PlanLoad::query()
        //     ->with(['lines.model.modelType', 'client', 'orderType'])
        //     ->get();

        $orders = Order::query()
            ->with(['lines.model.modelType', 'client', 'orderType'])
            ->get();

        return OrderRenderResource::collection($orders);

        $validData = $request->validate([
            'start' => 'required|date|beforeOrEqual:end',
            'end'   => 'required|date|afterOrEqual:start',
        ]);

        //        return apiDebug($validData);
        //        return $validData;

        $orders = Order::query()
            ->whereBetween('load_date', [$validData['start'], $validData['end']])
            ->get();
        //        $orders = Order::all();


        return new OrderCollection($orders);

        //        return $orders;

        //        return apiDebug($orders);
        //        return new OrderCollection(
        //            Order::whereBetween('unload_date', [$validData['start'], $validData['end']])
        //        );

    }


    /**
     * ___ Проверяем заявки на вшивость перед загрузкой в базу
     * @param Request $request
     * @return array
     */
    public function validateOrders(Request $request)
    {
        $data = $request->validate(['data' => 'required|json']);

        $orders = json_decode($data['data'], true);

        $validatedOrders = OrdersService::validateOrders($orders);

        return ['data' => $validatedOrders];
    }


    /**
     * ___ Загружаем заказы из браузера
     * @param Request $request
     * @return string
     */
    public function uploadOrders(Request $request)
    {
        // try {

        $data = $request->validate(['data' => 'required|json']);

        // TODO: Сделать проверку на валидность данных и соответствие шаблону

        $orders = json_decode($data['data'], true);
        // Дополнительная проверка структуры JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON structure');
        }

        // DB::table('orders')->truncate();

        // $orderDubs      = [];            // Дубли заказов
        // $missingModels  = [];            // Не найденные модели
        // $missingClients = [];            // Не найденные клиенты


        foreach ($orders as $order) {

            // __ Пропускаем игнорируемые заявки и игнорируемые клиенты
            if ($order[OrdersService::VALIDATE_FIELD][OrdersService::ACTION_FIELD] === OrdersService::ACTION_ORDER_IGNORE ||
                $order[OrdersService::VALIDATE_FIELD][OrdersService::ACTION_FIELD] === OrdersService::ACTION_CLIENT_IGNORE) {
                continue;
            }

            // __ Если нужно добавить клиента, то добавляем
            if ($order['client_id'] === 0 &&
                $order[OrdersService::VALIDATE_FIELD][OrdersService::ACTION_FIELD] === OrdersService::ACTION_CLIENT_ADD) {
                $createdClient = Client::query()->create([
                    'name'       => $order['client_full_name'],
                    'short_name' => $order['client_full_name'],
                    CODE_1C      => $order['client_code'],
                ]);

                if (!$createdClient) {
                    throw new Exception('Creating client with 1c code = ' . $order['client_code'] . ' failed');
                }
                continue;
            }

            // __ Пробуем найти клиента
            $client = Client::query()->find($order['client_id']);
            if (!$client) {
                $client = Client::query()->where(CODE_1C, $order['client_code'])->first();
            }

            // __ Если клиент не найден, то ошибка
            if (!$client) {
                throw new Exception('Client with id = ' . $order['client_id'] . ' not found');
            }

            // DB::transaction(function () use ($order, $client) {

            // __ Если нужно добавить заявку, то добавляем
            if ($order[OrdersService::VALIDATE_FIELD][OrdersService::ACTION_FIELD] === OrdersService::ACTION_ORDER_ADD) {

                // __ При добавлении в БД Заявки, может возникнуть следующие ситуации:
                // ___ 1. Заявка уже есть в БД, но она прогнозная, возможно, Сменные задания,
                // ___ которые ссылаются на эту Заявку, на разных участках, например, Пошиве - уже распределены в плане на части.
                // __ Решение: Будем распределять количество по распределенным частям. Если больше количество,
                // __ добавляем в последнюю часть Заявки, если не хватает - удаляем лишние части.
                // ___ 2. Заявки нет в БД.
                // __ Решение: Создаем Заявку и добавляем для нее стандартные Сменные задания.
                $needToDistribute = false;  // __ Нужно ли распределять количество по Сменным заданиям или создавать новые

                // __ Получаем Тип изделий в Заявках
                $elementsType    = OrdersService::getOrderElementsTypeFromFront($order);
                $elementsTypeRef = OrdersService::getOrderElementsTypeReference($elementsType);

                // __ Получаем тип заявки по номеру (гар. рем, серийная и т.д.)
                $orderType = OrdersService::getOrderTypeByOrderNoAndClientId($order['order_no'], $order['client_id']);

                // __ Если после проверки Заявка уже найдена в базе, то она должна быть прогнозной
                if ($order[OrdersService::VALIDATE_FIELD][OrdersService::ORDER_ID_FIELD] !== 0) {

                    $forecastOrder = Order::query()
                        ->with('lines')
                        ->find($order[OrdersService::VALIDATE_FIELD][OrdersService::ORDER_ID_FIELD]);

                    if (!$forecastOrder) {
                        throw new Exception('Forecast order with id = ' . $order[OrdersService::VALIDATE_FIELD][OrdersService::ORDER_ID_FIELD] . ' not found');
                    }

                    // __ Обновляем Прогнозную Заявку раскрытой Заявкой из 1С
                    $forecastOrder->client_id     = $client->id;
                    $forecastOrder->order_type_id = $orderType->id;    // __ Преобразуем из Прогнозного типа в соответствующий
                    // $forecastOrder->plan_load_id       = 0;                  // TODO: Повесить Observer и создать плановую загрузку, пока не будем управлять сущность;
                    // $forecastOrder->order_no_num       = parseNumericValue($order['order_no']);
                    $forecastOrder->order_no_str    = $order['order_no'];
                    $forecastOrder->order_no_origin = $order['order_no_1c'];

                    // $forecastOrder->elements_type      = $elementsType;
                    // $forecastOrder->elements_type_ref  = OrdersService::getOrderElementsTypeReference($elementsType);
                    $forecastOrder->responsible        = $order['responsible'];
                    $forecastOrder->manager_load_date  = $order['load_at_1c'] === '' ? null : Carbon::parse($order['load_at_1c']);
                    $forecastOrder->manager_start      = $order['mg_start'] === '' ? null : Carbon::parse($order['mg_start']);
                    $forecastOrder->manager_end        = $order['mg_end'] === '' ? null : Carbon::parse($order['mg_end']);
                    $forecastOrder->design_start       = $order['kb_start'] === '' ? null : Carbon::parse($order['kb_start']);
                    $forecastOrder->design_end         = $order['kb_end'] === '' ? null : Carbon::parse($order['kb_end']);
                    $forecastOrder->no_1c              = $order['order_no_1c'];
                    $forecastOrder->code_1c            = $order['order_code'];
                    $forecastOrder->base_order_code_1c = $order['base'];
                    $forecastOrder->comment_1c         = $order['comment'];
                    $forecastOrder->client_code_1c     = $order['client_code'];
                    $forecastOrder->client_name_1c     = $order['client_full_name'];
                    $forecastOrder->service            = $order['service'];
                    $forecastOrder->unload_at          = $order['unload_at'] === '' ? null : Carbon::parse($order['unload_at']);
                    $forecastOrder->is_forecast         = false;    // __ Прогнозная заявка раскрывается

                    // __ Вставляем именно массивом, без преобразования в json. Пока ничего не меняем
                    // $forecastOrder->amounts = [
                    //     'mattresses' => 0,
                    //     'up_covers'  => 0,
                    //     'averages'   => 0,
                    //     'covers'     => 0,
                    //     'children'   => 0,
                    //     'formats'    => 0,
                    //     'unknowns'   => 0,
                    //     'totals'     => 0,
                    // ];

                    // Warn: Тут по желанию, оставлять дату загрузки, с которой была создана прогнозная заявка
                    // Warn: или обновлять ее дату из 1С
                    // $forecastOrder->load_at      = Carbon::parse($order['load_at']); // __ Пока не будем обновлять дату загрузки
                    // $forecastOrder->order_period = PlanService::getOrderPeriod($order['load_at']); // __ Пока не будем обновлять период

                    $forecastOrder->save();

                    $needToDistribute = true;

                } else {

                    // __ Иначе создаем новую Заявку
                    $createdOrder = Order::query()->create([
                        'client_id'         => $client->id,
                        'order_type_id'     => $orderType->id,
                        // 'plan_load_id'      => null,                  // TODO: Повесить Observer и создать плановую загрузку, пока не будем управлять сущностью
                        'order_no_num'      => parseNumericValue($order['order_no']),
                        'order_no_str'      => $order['order_no'],
                        'order_no_origin'   => $order['order_no_1c'],
                        'order_period'      => PlanService::getOrderPeriod($order['load_at']),
                        'elements_type'     => $elementsType,
                        'elements_type_ref' => $elementsTypeRef,

                        // Вставляем именно массивом, без преобразования в json
                        'amounts'           => [
                            'mattresses' => 0,
                            'up_covers'  => 0,
                            'averages'   => 0,
                            'covers'     => 0,
                            'children'   => 0,
                            'formats'    => 0,
                            'unknowns'   => 0,
                            'totals'     => 0,
                        ],

                        'responsible'        => $order['responsible'],
                        'manager_load_date'  => $order['load_at_1c'] === '' ? null : Carbon::parse($order['load_at_1c']),
                        'manager_start'      => $order['mg_start'] === '' ? null : Carbon::parse($order['mg_start']),
                        'manager_end'        => $order['mg_end'] === '' ? null : Carbon::parse($order['mg_end']),
                        'design_start'       => $order['kb_start'] === '' ? null : Carbon::parse($order['kb_start']),
                        'design_end'         => $order['kb_end'] === '' ? null : Carbon::parse($order['kb_end']),
                        'no_1c'              => $order['order_no_1c'],
                        'code_1c'            => $order['order_code'],
                        'base_order_code_1c' => $order['base'],
                        'comment_1c'         => $order['comment'],
                        'client_code_1c'     => $order['client_code'],
                        'client_name_1c'     => $order['client_full_name'],
                        'service'            => $order['service'],
                        'load_at'            => Carbon::parse($order['load_at']),
                        'unload_at'          => $order['unload_at'] === '' ? null : Carbon::parse($order['unload_at']),
                        'is_forecast'        => false,      // __ Заявка раскрывается

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
                        throw new Exception('Creating order with 1c code = ' . $order['order_code'] . ' failed');
                    }
                }

                // __ Добавляем контекст Заявки (OrderLines)
                foreach ($order['items'] as $orderLine) {

                    // __ Получаем размеры
                    $dims = SizeService::getDimensions($orderLine['s']);

                    // __ Пробуем найти модель
                    $findModel = ModelsService::getModelByCode1C($orderLine['c']);

                    if (!$findModel) {
                        throw new Exception('Model with code 1c = ' . $orderLine['c'] . ' not found');
                    }

                    // __ Собираем в массив не найденные модели
                    // if (!$findModel) {
                    //     $missingModels[$orderLine['c']] = $orderLine['n'];
                    //     continue;
                    // }

                    /** @var Order $forecastOrder */
                    /** @var Order $createdOrder */
                    $createLine = OrderLine::query()->create(
                        [
                            'order_id'      => $needToDistribute ? $forecastOrder->id : $createdOrder->id,
                            'size'          => $orderLine['s'],
                            'width'         => $dims->getWidth(),
                            'length'        => $dims->getLength(),
                            'height'        => $dims->getHeight(),
                            'model_name'    => $orderLine['n'],
                            'model_code_1c' => $findModel->code_1c,
                            'amount'        => $orderLine['a'],
                            'textile'       => $orderLine['t'],
                            'composition'   => $orderLine['d'],
                            'describe_1'    => $orderLine['d1'],
                            'describe_2'    => $orderLine['d2'],
                            'describe_3'    => $orderLine['d3'],
                            // 'active'        => $orderLine[''],
                            // 'status'        => $orderLine[''],
                            // 'description'   => $orderLine[''],
                            // 'comment'       => $orderLine[''],
                            // 'note'          => $orderLine[''],
                            // 'meta'          => $orderLine[''],
                        ]
                    );
                }


                $targetOrder = $needToDistribute ? $forecastOrder : $createdOrder;

                // __ Устанавливаем статус Заявки - Создано
                $targetOrder->statuses()->attach([
                    OrderStatus::ORDER_STATUS_LOADED_FROM_1C_ID => [
                        'set_at'     => now(),
                        'created_by' => auth()->id(),
                    ]
                ]);

                // __ Если тип элементов в Заявке - не матрасы, то пропускаем
                // __ Создаем СЗ на Участки только для матрасов
                if ($targetOrder->elements_type_ref !== ElementTypes::MATTRESSES->value) {
                    continue;
                }

                // __ Тут добавляем или распределяем СЗ на нужные участки
                if ($needToDistribute) {

                    // __ Распределяем СЗ на Пошив
                    /** @var Order $forecastOrder */
                    $result = SewingService::distributeSewingTaskFromOrderId($forecastOrder->id);
                    if (!$result) {
                        throw new Exception('Error while distributing Sewing Task with Client id = ' . $client->id);
                    }

                    // __ Распределяем СЗ на Раскрой
                    // __ ...

                    // __ Распределяем СЗ на Сборку
                    // __ ...

                    // __ И только после этого удаляем строки с кодом средней модели и автоматом!!! удаляем их из всех СЗ
                    // __ Получаем код средней модели по клиенту и типу элементов в Заявке
                    $averageModelCode = ModelsService::getAverageModelCodeByClientAndElementsType($order->client, $order->elements_type_ref);

                    // __ Удаляем строки с кодом средней модели и автоматом удаляем их из СЗ
                    $order->lines()->where('model_code_1c', $averageModelCode)->delete();


                } else {
                    // __ Создаем СЗ на Пошив
                    /** @var Order $createdOrder */
                    $sewingTask = SewingService::createSewingTaskFromOrderId($createdOrder->id);
                    if (!$sewingTask) {
                        throw new Exception('Error while creating Sewing Task with Client id = ' . $client->id);
                    }

                    // __ Создаем СЗ на Раскрой
                    // __ ...

                    // __ Создаем СЗ на Сборку
                    // __ ...

                }
            }
            // }); // transaction
        }


        // $a = $orderDubs;            // Дубли заказов
        // $b = $missingModels;        // Не найденные модели
        // $c = $missingClients;       // Не найденные клиенты

        // return 'done...';

        return EndPointStaticRequestAnswer::ok();
        // } catch (Exception|Throwable $e) {
        //     return EndPointStaticRequestAnswer::fail($e);
        // }
    }


    public function deleteOrders(Request $request)
    {
        $ids = $request->input('ids');

        try {
            Order::whereIn('id', $ids)->delete();
            return response()->json([
                'success' => 'Заказы удалены'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }

        //        $data = $request->all();
        //        return view('dd', ['data' => $data]);

        //        return $ids;
    }


    /**
     * ___ Получаем все типы Заявок
     * @return AnonymousResourceCollection|string
     */
    public function getOrderTypes()
    {
        try {
            $orderTypes = OrderType::all();
            return OrderTypeResource::collection($orderTypes);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Обновляем цвет Типа Заявки
     * @param Request $request
     * @return string
     */
    public function patchOrderTypeColor(Request $request)
    {
        try {
            $validated = $request->validate([
                'data'       => 'required|array',
                'data.color' => 'required|hex_color',
                'data.id'    => 'required|exists:order_types,id'
            ]);

            $validated = $validated['data'];

            OrderType::query()->findOrFail($validated['id'])->update(['color' => $validated['color']]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
