<?php

namespace App\Http\Controllers\Api\V1\Cells\sewing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Sewing\CellSewingAutoCollection;
use App\Http\Resources\Order\OrderCollection;
use App\Models\Manufacture\Cells\Sewing\CellSewingAuto;
use App\Models\Manufacture\Cells\Sewing\CellSewingHard;
use App\Models\Manufacture\Cells\Sewing\CellSewingLight;
use App\Models\Manufacture\Cells\Sewing\CellSewingUniversal;
use App\Models\Order\Order;
use Illuminate\Http\Request;

class CellSewingController extends Controller
{
    public function getSewingCellData(Request $request, string $type = '')
    {
        $result = json_encode(['sewing' => '']);

        $validData = $request->validate([
            'start' => 'required|date|beforeOrEqual:end',
            'end' => 'required|date|afterOrEqual:start',
        ]);

//        $orders = Order::whereBetween('load_date', [$validData['start'], $validData['end']])->get();
//        $orders = Order::all();

        //todo Переделать через Scope

        switch ($type) {
            case 'auto':
                $result = CellSewingAuto::query()->whereBetween('manufactured_at', [$validData['start'], $validData['end']])->get();
                break;
            case 'universal':
                $result = CellSewingUniversal::query()->whereBetween('manufactured_at', [$validData['start'], $validData['end']])->get();
                break;
            case 'hard':
                $result = CellSewingHard::query()->whereBetween('manufactured_at', [$validData['start'], $validData['end']])->get();
                break;
            case 'light':
                $result = CellSewingLight::query()->whereBetween('manufactured_at', [$validData['start'], $validData['end']])->get();
        }

        // Получаем уникальные id заказов
        $ordersIds = $this->getUniqueOrdersIds($result);

        // Получаем сами заказы
        $orders = Order::query()->whereIn('id', $ordersIds)->get();

        // todo Переделать через Resources
//        return [new CellSewingAutoCollection($result), new OrderCollection($orders)];
        return json_encode(['sewing' => ['cell_data' => new CellSewingAutoCollection($result), 'orders' => new OrderCollection($orders)]]);
//        return json_encode(['sewing' => ['cell_data' => $result, 'orders' => $orders]]);


//        return new CellSewingAutoCollection($result);

//        return json_encode(['sewing' => 'sewing controller '.$type]);
    }


    /**
     * Возвращает массив уникальных id заказов
     * @param mixed $data
     * @return array
     */
    private function getUniqueOrdersIds(mixed $data): array
    {
        $result = [];

        if (is_null($data) || is_bool($data)) {
            return $result;
        }

        foreach ($data as $item) {
            $result[] = $item['line']['order_id'];
        }


        return array_unique($result);
    }

}
