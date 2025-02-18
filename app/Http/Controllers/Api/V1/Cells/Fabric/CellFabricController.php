<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fabric\FabricCollection;
use App\Http\Resources\Fabric\FabricResource;
use App\Models\Manufacture\Cells\Fabric\Fabric;
use Illuminate\Http\Request;

class CellFabricController extends Controller
{
    /**
     * Attract: Загружаем данные из отчета 1С
     * @param Request $request
     * @return array|string
     */
    public function uploadFabrics(Request $request)
    {
        // Info: Структура выгружаемых данных
        /*
        [
            {
                "c":"000034799",                             Код по 1С
                "n":"ПС 142МТ 8П 30С граф (рис. ГР)"         Название ПС
            },
            {
                "c":"000028434",
                "n":"ПС 142Т 8П 30С Maxx (рис. Ж5)"
            }
        ]
        */

        // todo Сделать проверку на валидность данных массива fabrics.json

        $fabrics = $request->all();
        $dubs = [];

        foreach ($fabrics as $fabric) {

            $checkFabric = Fabric::query()
                ->where('code_1C', $fabric['c'])
                ->first();

            if ($checkFabric) {
                $dubs[] = $fabric['n'];
            } else {
                Fabric::query()->create([
                    'code_1C' => $fabric['c'],
                    'name' => $fabric['n'],
                ]);
            }

        }

        if (count($dubs)) {
            return $dubs;
        }

        return OK_STATUS_WORD;
    }

    /**
     * Attract: Возвращаем ПС по коду 1C
     * @param $code1C
     * @return FabricResource
     */
    public function fabric($code1C)
    {
        return new FabricResource(Fabric::query()->find($code1C));
    }

    /**
     * Attract: Возвращаем список ПС
     * @return FabricCollection
     */
    public function fabrics()
    {
        return new FabricCollection(
            Fabric::query()
                ->orderBy('name')
                ->with('fabricMachine')
                ->get()
        );

    }

}
