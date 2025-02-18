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
     * Attract: Возвращаем ПС по id
     * @param $id
     * @return FabricResource
     */
    public function fabric($id)
    {
        return new FabricResource(Fabric::query()->find($id));
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

    /**
     * Attract Обновляем ПС
     * @param Request $request
     * @return string
     */
    public function update(Request $request)
    {
        // todo Сделать проверку на валидность данных изменяемого ПС
        $fabricPayload = $request->input('data');           // получаем данные из запроса
        $fabric = Fabric::query()->find($fabricPayload['id']);   // ищем сотрудника по id в базе

        if ($fabric) {
            $fabric->name = $fabricPayload['name'];
            $fabric->buffer_amount = $fabricPayload['buffer'];
            $fabric->optimal_party = $fabricPayload['optimal_party'];
            $fabric->description = $fabricPayload['description'];
            $fabric->active = $fabricPayload['active'];
            $fabric->fabric_machine_id = $fabricPayload['fabric_machine']['id'];
            $fabric->save();
            return OK_STATUS_WORD;
        }
        return FAIL_STATUS_WORD;
    }

    /**
     * Attract Создаем ПС
     */
    public function create(Request $request)
    {
        // todo Сделать проверку на валидность данных создаваемого ПС
        $fabricPayload = $request->input('data');           // получаем данные из запроса
        $fabric = Fabric::query()->create([
            'name' => $fabricPayload['name'],
            'buffer_amount' => $fabricPayload['buffer'],
            'optimal_party' => $fabricPayload['optimal_party'],
            'description' => $fabricPayload['description'],
            'active' => $fabricPayload['active'],
            'fabric_machine_id' => $fabricPayload['fabric_machine']['id']
        ]);

        if ($fabric) {
            return OK_STATUS_WORD;
        }
        return FAIL_STATUS_WORD;
    }


    /**
     * Attract Удаляем ПС
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $fabric = Fabric::query()->find($id);
        if ($fabric) {
            $fabric->delete();
            return OK_STATUS_WORD;
        }

        return FAIL_STATUS_WORD;

    }
}
