<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Http\Controllers\Controller;
use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricExpense;
use App\Models\Manufacture\Cells\Fabric\FabricOrder;
use App\Services\Manufacture\FabricService;
use Illuminate\Http\Request;

class CellFabricOrderController extends Controller
{

    /**
     * Attract Загрузка расхода ПС из отчета СВПМ
     * @param Request $request
     * @return mixed
     */
    public function uploadFabricOrders(Request $request)
    {
        $fabricOrders = $request->all();

        // todo Сделать проверку на валидность данных в FabricOrders - отчет 1С - СВПМ

        $dubs = [];

        foreach ($fabricOrders as $fabricOrder) {

            // проверяем есть ли такой заказ по коду из 1С
            $checkFabricOrder = FabricOrder::query()
                ->where('code_1C', $fabricOrder['code_1C'])
                ->first();

//            $checkFabricOrder = FabricOrder::query()
//                ->where('client_id', $fabricOrder['cl_id'])
//                ->where('order_no', $fabricOrder['order_no'])
//                ->first();

            if ($checkFabricOrder) {
                $dubs[] = $fabricOrder;

            } else {

                $newFabricOrder = FabricOrder::query()->create([
                    'client_id' => $fabricOrder['cl_id'],
                    'order_no' => $fabricOrder['order_no'],
                    'raw_text' => $fabricOrder['raw'],
                    'code_1C' => $fabricOrder['code_1C'],
                    'time_1C' => $fabricOrder['moment'],
                    'type' => $fabricOrder['type'],
                ]);

                $newFabricOrderId = $newFabricOrder->id;                    // Дергаем ID для заполнения FabricExpense

                foreach ($fabricOrder['fabrics'] as $fabricExpense) {

                    $fabric = FabricService::getFabricByName($fabricExpense['name']);

                    if (is_null($fabric)) {
                        return 'Не найдено в базе ПС: ' . $fabricExpense['name'];
                    }

                    $newFabricExpense = FabricExpense::query()->create([
                        'fabric_order_id' => $newFabricOrderId,
                        'fabric_id' => $fabric->id,
                        'name' => $fabricExpense['name'],
                        'expense' => $fabricExpense['expense'],
                    ]);
                }

            }

        }

        if (count($dubs) === 0) {
            return OK_STATUS_WORD;
        }
        return ($dubs);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
