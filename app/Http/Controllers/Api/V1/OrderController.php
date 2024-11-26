<?php

namespace App\Http\Controllers\V1;

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
}
