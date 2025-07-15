<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\CellsGroups\CellsGroupsCollection;
use App\Models\Manufacture\CellsGroup;
use Illuminate\Http\Request;

class CellsGroupController extends Controller
{
    //attract: Получаем список всех Групп ПЯ
    public function getCellsGroups()
    {
//        return json_encode(['cells_groups' => '11111111111111111111']);
        return new CellsGroupsCollection(
            CellsGroup::query()->with(['cellItems'])->get()
        );
    }
}
