<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    // Отдаем заказы за период
    public function getOrders(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        return response()->json([
            'start' => $start,
            'end' => $end
        ]);
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
            "order_no": 456,                'o'
            "load": "25.01.2025",           'l'
            "unload": "25.01.2025",         'u'
            "date_1C": "25.01.2025",        'd'
            "meta": "Text"                  'meta'
            "remark": "Comment"             'r'
            "status": 10                    's'
        },
    "order_data":
        [
            {
                "size": "160x200x20",       's'
                "model": "F5",              'm'
                "amount": 5,                'a'
                "textile": "кб",            't'
                "comment": "commm",         'c'
                "describe_1": "d1",         'd1'
                "describe_2": "d2",         'd2'
                "describe_3": "d3"          'd3'
            }
        ]
  },
  {}
]
*/

        return $request->all();
//        Log::info('User data:',  [$request]);

//        $orders = $request->all();
        $orders = $request->json()->all();
//        Log::info('User data:',  $orders);


        foreach ($orders as $order) {
//            $this->validate($order, []


            Log::info('value:', [$order['order']]);


        }


//        Log::info('User data:', [$orders[0]]);


        return 0;

        Log::info('isJson:', [$request->expectsJson()]);

//        $data = json_decode($request->getContent(), true);
        $data = $request->getContent();

        Log::info('data:', [$data]);
        Log::info('data_val:', [json_decode($data)]);

        $data_json = json_encode($data);
//        return $data_json;
        return json_last_error_msg();


        return $request->expectsJson();

        $orders = $request->json()->all();

        return $orders;

        Log::info('User data:', $orders);

//        foreach ($orders as $order) {
//            Log::info('order:', $order['order']);
////            foreach ($order->order_data as $line) {
////                Log::info('line:', $line);
////            }
//        }

//        Log::info('data:', $request->header());

//        var_dump($orders);


//        return response()->json([
//            'start' => 1,
//            'end' => 2
//        ]);

//        return $orders;
//        return response()->json($orders);
        return 'Data Received';
    }

}
