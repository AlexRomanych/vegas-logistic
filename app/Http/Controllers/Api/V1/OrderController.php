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

//        $orders = $request->all();
        $orders = $request->json()->all();
//        $orders = $request->file();
//        $orders = $request->query('file');
//        $orders =$request->json()->all();

        Log::info('User data:',  $orders);
        Log::info('headers:', $request->header());
//        Log::info('data:', $request->header());

//        var_dump($orders);


//        return response()->json([
//            'start' => 1,
//            'end' => 2
//        ]);

        return $orders;
//        return response()->json($orders);
    }


}
