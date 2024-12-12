<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Order\OrderCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\Line;
use App\Models\Order\Order;

class OrderController extends Controller
{

    // Отдаем заказы за период
    public function getOrders(Request $request)
    {
//        return apiDebug($request->all());

        $validData = $request->validate([
            'start' => 'required|date|beforeOrEqual:end',
            'end' => 'required|date|afterOrEqual:start',
        ]);

//        return apiDebug($validData);
//        return $validData;

        $orders = Order::whereBetween('unload_date', [$validData['start'], $validData['end']])->get();
//        $orders = Order::all();


        return new OrderCollection($orders);

//        return $orders;

//        return apiDebug($orders);
//        return new OrderCollection(
//            Order::whereBetween('unload_date', [$validData['start'], $validData['end']])
//        );

    }

// Загружаем заказы из браузера
// todo доделать поверку на валидность данных
    public function uploadOrders(Request $request)
    {

        /*
            Info: Структура выгружаемых данных
        [
          {
            "order":                                Объект заказа
                {
                    "client_id": 5,                 'c'
                    "order_no": 456,                'n'
                    "load": "25.01.2025",           'l'
                    "unload": "25.01.2025",         'u'
                    "manufacture": "25.01.2025",    'm'
                    "date_1C": "25.01.2025",        'd'
                    "meta": "Text"                  'meta'
                    "remark": "Comment"             'r'
                    "status": 10                    's'
                    "short_name": "ЛММ_Брест"       'sn'            не используем
                },
            "order_data":
                [
                    {
                        "size": "160x200x20",       's'
                        "model": "F5",              'm'
                        "amount": 5,                'a'
                        "textile": "кб",            't'
                        "comment": "comment",       'c'
                        "describe_1": "d1",         'd1'
                        "describe_2": "d2",         'd2'
                        "describe_3": "d3"          'd3'
                    }
                ]
          },
          {}
        ]
        */

        $orders = $request->all();

        // todo проверка на валидность данных

        $dubs = [];

        // обходим все заказы
        foreach ($orders as $orderBag) {

            $order = $orderBag['order'];        // берем всю инфу о заказе

            // проверяем есть ли такой заказ
            $checkOrder = Order::where('client_id', $order['c'])
                ->where('no_num', $order['n'])
                ->first();

            if ($checkOrder) {
                $dubs[] = $order;

            } else {
                // создаем новый заказ
                $newOrder = Order::create([
                    'client_id' => $order['c'],
//                    'client_id' => 1,               // todo заменить

                    'no_num' => $order['n'],
                    'load_date' => $order['l'],
                    'unload_date' => $order['u'],
                    'manuf_date' => $order['m'],
//                        'date_1C' => $order['d'],
                    'meta' => $order['meta'],
                    'description' => $order['r'],
                    'status' => $order['s']
                ]);

                $newOrderId = $newOrder->id;

                foreach ($orderBag['order_data'] as $line) {
                    $newLine = new Line([
                        'order_id' => $newOrderId,
                        'size' => $line['s'],
                        'model' => $line['m'],
                        'amount' => $line['a'],
                        'textile' => $line['t'],
                        'comment' => $line['c'],
                        'describe_1' => $line['d1'],
                        'describe_2' => $line['d2'],
                        'describe_3' => $line['d3']
                    ]);

                    $newLine->save();

                }
            }
        }

        return $dubs;           // возвращаем дубликаты

//        return response()->json([
//            'dubs' => $dubs
//        ]);


        $data = $request->all();
        return view('dd', ['data' => $data]);

        return $request->all();

        return 'Data Received';
    }

}
