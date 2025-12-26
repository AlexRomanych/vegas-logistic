<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Facades\Model;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\Render\OrderRenderResource;
use App\Models\Client;
use App\Models\Manufacture\Cells\sewing\CellSewingAuto;
use App\Models\Manufacture\Cells\sewing\CellSewingHard;
use App\Models\Manufacture\Cells\sewing\CellSewingLight;
use App\Models\Manufacture\Cells\sewing\CellSewingUniversal;
use App\Models\Order\Line;
use App\Models\Order\Order;
use App\Models\Order\OrderLine;
use App\Services\ModelsService;
use App\Services\OrdersService;
use App\Services\PlanService;
use App\Services\SizeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    // Отдаем заказы за период
    public function getOrders(Request $request)
    {

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

    // ___ Проверяем заявки на вшивость перед загрузкой в базу
    public function validateOrders(Request $request)
    {
        $data = $request->validate(['data' => 'required|json']);

        $orders = json_decode($data['data'], true);

        $validatedOrders = OrdersService::validateOrders($orders);


        return $validatedOrders;
    }



    // Загружаем заказы из браузера
    // todo доделать поверку на валидность данных
    public function uploadOrders(Request $request)
    {

        $data = $request->validate(['data' => 'required|json']);

        // TODO: Сделать проверку на валидность данных и соответствие шаблону


        $orders = json_decode($data['data'], true);

        $validatedOrders = OrdersService::validateOrders($orders);


        $orderDubs = [];            // Дубли заказов
        $missingModels = [];        // Не найденные модели
        $missingClients = [];       // Не найденные клиенты


        DB::table('orders')->truncate();


        foreach ($orders as $order) {

            // __ Пробуем найти клиента
            $client = Client::query()->find($order['client_id']);

            if (!$client) {
                $missingClients[$order['client_id']] = [
                    'name'           => $order['client_full_name'],
                    'client_code_1c' => $order['client_code'],
                ];
                continue;
            }

            // __ Получаем тип элементов в Заявке
            $elementsType = OrdersService::getOrderElementsTypeFromFront($order);

            // __ Пробуем найти заказ
            $findOrder = Order::query()
                ->where('client_id', $order['client_id'])
                ->where('order_no_num', (float)$order['order_no'])
                ->where('elements_type', $elementsType)
                ->first();

            if ($findOrder) {
                $orderDubs[] = $order;
                continue;
            }

            // __ Получаем тип заявки по номеру (гар. рем, серийная и т.д.)
            $orderType = OrdersService::getOrderTypeByOrderNoAndClientId($order['order_no'], $order['client_id']);

            $createdOrder = Order::query()->create([
                'client_id'         => $client->id,
                'order_type_id'     => $orderType->id,
                'plan_load_id'      => 0,                  // TODO: Повесить Observer и создать плановую загрузку, пока не будем управлять сущностью
                'order_no_num'      => parseNumericValue($order['order_no']),
                'order_no_str'      => $order['order_no'],
                'order_no_origin'   => $order['order_no_1c'],
                'order_period'      => PlanService::getOrderPeriod($order['load_at']),
                'elements_type'     => $elementsType,
                'elements_type_ref' => OrdersService::getOrderElementsTypeReference($elementsType),

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
                'unload_at'          => $order['unload_at'] === '' ? null : Carbon::parse($order['unload_at']), //' $order['service'],
                // 'status'             => $order[''],
                // 'is_forecast'        => $order[''],
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


            foreach ($order['items'] as $orderLine) {

                // __ Получаем размеры
                $dims = SizeService::getDimensions($orderLine['s']);

                // __ Пробуем найти модель
                $findModel = ModelsService::getModelByCode1C($orderLine['c']);

                if (!$findModel) {
                    $missingModels[$orderLine['c']] = $orderLine['n'];
                    continue;
                }

                $createLine = OrderLine::query()->create(
                    [
                        'order_id'      => $createdOrder->id,
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


        }

        $a = $orderDubs;            // Дубли заказов
        $b = $missingModels;        // Не найденные модели
        $c = $missingClients;       // Не найденные клиенты


        return 'done...';

        $result = OrdersService::validateOrders($orders);

        //        return $orders;
        // todo проверка на валидность данных

        $dubs = [];

        // обходим все заказы
        foreach ($orders as $orderBag) {

            $order = $orderBag['order'];        // берем всю инфу о заказе

            // проверяем есть ли такой заказ
            $checkOrder = Order::where('client_id', (int)$order['c'])
                ->where('no_num', (float)($order['n']))
                ->first();

            if ($checkOrder) {
                $dubs[] = $order;

            } else {
                // создаем новый заказ
                // Создаем через Create, потому что возвращает объект
                // todo проверить на валидность данных

                $newOrder = Order::create([
                    'client_id' => $order['c'],
                    //'client_id' => 1,               // todo заменить

                    'no_num'      => $order['n'],
                    'load_date'   => $order['l'],
                    'unload_date' => $order['u'],
                    'manuf_date'  => $order['m'],
                    // 'date_1C' => $order['d'],
                    'meta'        => $order['meta'],
                    'description' => $order['r'],
                    'status'      => $order['s']
                ]);

                //                return $newOrder;
                $newOrderId = $newOrder->id;


                $sewingAuto = [];               // АШМ
                $sewingUniversal = [];          // УШМ
                $sewingSolidHard = [];          // Обшивка Light
                $sewingSolidLight = [];         // Обшивка Hard
                $sewingUndefined = [];          // Неопознанные


                foreach ($orderBag['order_data'] as $line) {
                    //                    $newLine = new Line([
                    //                        'order_id' => $newOrderId,
                    //                        'size' => $line['s'],
                    //                        'model' => $line['m'],
                    //                        'amount' => $line['a'],
                    //                        'textile' => $line['t'],
                    //                        'comment' => $line['c'],
                    //                        'describe_1' => $line['d1'],
                    //                        'describe_2' => $line['d2'],
                    //                        'describe_3' => $line['d3']
                    //                    ]);
                    //
                    //                    $newLine->save();

                    $newLine = Line::create([
                        'order_id'   => $newOrderId,
                        'size'       => $line['s'],
                        'model'      => $line['m'],
                        'amount'     => $line['a'],
                        'textile'    => $line['t'],
                        'comment'    => $line['c'],
                        'describe_1' => $line['d1'],
                        'describe_2' => $line['d2'],
                        'describe_3' => $line['d3']
                    ]);

                    // Создаем запись для добавления в ПЯ швейки
                    $sewingLine = [
                        'amount'          => $line['a'],
                        'line_id'         => $newLine->id,
                        'manufactured_at' => $newOrder->manufacture_date_opp,
                    ];

                    // Пользуем Фасад
                    if (Model::isSewingAuto($line['m'])) {
                        $sewingAuto[] = $sewingLine;
                    } else if (Model::isSewingUniversal($line['m'])) {
                        $sewingUniversal[] = $sewingLine;
                    } else if (Model::isSewingSolidHard($line['m'])) {
                        $sewingSolidHard[] = $sewingLine;
                    } else if (Model::isSewingSolidLight($line['m'])) {
                        $sewingSolidLight[] = $sewingLine;
                    } else {
                        $sewingUndefined[] = $sewingLine;
                    }


                }

                CellSewingAuto::insert($sewingAuto);                    // Вставляем данные в швейку АШМ
                CellSewingUniversal::insert($sewingUniversal);          // Вставляем данные в швейку УШМ
                CellSewingHard::insert($sewingSolidHard);               // Вставляем данные в швейку Обшивка Hard
                CellSewingLight::insert($sewingSolidLight);             // Вставляем данные в швейку Обшивка Light

            }
        }


        //        return 1111;
        //        return $dubs;           // возвращаем дубликаты

        return response()->json([
            'dubs' => $dubs
        ]);


        //        $data = $request->all();
        //        return view('dd', ['data' => $data]);
        //
        //        return $request->all();
        //
        //        return 'Data Received';
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


}
