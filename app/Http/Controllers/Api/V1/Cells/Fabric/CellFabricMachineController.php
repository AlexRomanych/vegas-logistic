<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricMachineCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricMachineResource;
use App\Models\Manufacture\Cells\Fabric\FabricMachine;

class CellFabricMachineController extends Controller
{
    /**
     * Attract: Возвращаем список стегальных машин
     * @return FabricMachineCollection
     */
    public function machines()
    {
        return new \App\Http\Resources\Manufacture\Cells\Fabric\FabricMachineCollection(FabricMachine::all());
    }

    /**
     * Attract: Возвращаем стегальную машину по id
     * @param $id
     * @return \App\Http\Resources\Manufacture\Fabric\\App\Http\Resources\Manufacture\Cells\Fabric\FabricMachineResource
     */
    public function machine($id)
    {
        // todo Сделать проверку на существование машины
        return new FabricMachineResource(FabricMachine::query()->find($id));
    }



}
