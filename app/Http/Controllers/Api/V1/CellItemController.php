<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\CellItem\CellItemCollection;
use App\Models\Manufacture\CellItem;
use Illuminate\Http\Request;

class CellItemController extends Controller
{
    public function getCells()
    {
        //        return json_encode(['cells_groups' => '11111111111111111111']);
        return new CellItemCollection(
            CellItem::all()
        );
    }
}
