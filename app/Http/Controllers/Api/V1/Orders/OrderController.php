<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Facades\Model;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Models\Manufacture\Cells\sewing\CellSewingAuto;
use App\Models\Manufacture\Cells\sewing\CellSewingHard;
use App\Models\Manufacture\Cells\sewing\CellSewingLight;
use App\Models\Manufacture\Cells\sewing\CellSewingUniversal;
use App\Models\Order\Line;
use App\Models\Order\Order;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    // Отдаем заказы за период
    public function getOrders(Request $request)
    {
        //        return apiDebug($request->all());

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





    }



    // Загружаем заказы из браузера
    // todo доделать поверку на валидность данных
    public function uploadOrders(Request $request)
    {

        $data = $request->validate(['data' => 'required|json']);

        $orders = json_decode($data['data'], true);


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
