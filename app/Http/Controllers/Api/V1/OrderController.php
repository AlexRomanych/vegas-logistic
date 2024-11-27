<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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
//        $start = $request->query('start');
//        $end = $request->query('end');

//        return response()->json([
//            'start' => $start,
//            'end' => $end
//        ]);
        return response()->json([
            'start' => 1,
            'end' => 2
        ]);

        return response()->json($request);
    }


}
