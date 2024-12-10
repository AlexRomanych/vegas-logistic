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


    public function uploadOrders(Request $request)
    {
        Log::info('request:', [$request]);

//            $a = $request;

//        Log::info('request:', [$a]);

        $file = $request->file('file_out');
//
//        // Генерация уникального имени файла
        $fileName = time() . '_' . $file->getClientOriginalName();


//        $fileName = $request->_method;
//        Log::info('request:', [$fileName]);


//        $orders = $request->all('file')->getErrorMessage();

        $orders = $request->all();
//        $orders = $request->json()->all();
//        $orders = $request['file'];
//        $orders = $request->file('___orders___');
//        $orders = $request->query('file');
//        $orders =$request->json()->all();

//        $params = $request['params'];
//        Log::info('User data:',  [$params]);


        Log::info('User data:',  $orders);
//        Log::info('headers:', $request->header());
//        Log::info('data type:', [gettype($orders), count($orders)]);

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
    }


}
