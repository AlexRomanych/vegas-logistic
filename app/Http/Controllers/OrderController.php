<?php

namespace App\Http\Controllers;

use App\Contracts\VegasDataGetContract;
use App\Models\Order\AssemblyPart;
use App\Models\Order\Line;
use App\Models\Order\Order;
use App\Services\OrdersService;
use App\Services\SharedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class OrderController extends Controller
{

    public function index(SharedService $sharedService, OrdersService $ordersService, Request $request)
    {
        $period = $sharedService->validPlanDates($request);
        $orders = $ordersService->selectOrders($period);

        // Проверяем на загрузку в 1С
        foreach ($orders as $key => $order) {
            $orders[$key]['open_status'] = \App\Facades\Order::isOpenOrder($order);
        }


        return view('pages.orders.index', ['orders' => $orders]);

    }



    public function show($id)
    {
        $order = Order::query()->with('assemblyParts')->find($id);

        if (is_null($order)) {
            abort(404);
        }

        return view('pages.orders.show', ['order' => $order]);

        dd($order);
//        $ap = Order::query()->assemblyParts;
        $ap = $order->assemblyParts;

        dd($ap);
//        dd($order);

    }


    public function lineTest($id)
    {
//        $line = Line::query()->toBase()->find($id);
        $line = Line::query()->find($id)->toArray();
//        $line = Line::query()->find($id);

//        dump($line->test_name);
        dd($line);
//
//        dd($line->toJson(JSON_UNESCAPED_UNICODE));
//        dd($line->test_name);
////        dd($line->model_name);
//
//        dd($line);
    }

    public function assTest($id)
    {
//        $line = Line::query()->toBase()->find($id);
        $ass = AssemblyPart::query()->find($id);

        dd($ass->toArray());
    }


    // Заполняем данные из отчета ЕПС: Заказы + сборка + сами данные
    public function fillDataFromEPSReport(VegasDataGetContract $getter)
    {
        App::make(OrdersService::class, [$getter])->updateData();

//        return view('welcome');
    }
}
