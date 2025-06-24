<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricPictureSchemaResource;
use App\Models\FabricPictureSchema;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
//use Illuminate\Http\Request;

class CellFabricPictureSchemaController extends Controller
{

    /**
     * Descr: Получение списка рисунков ПС
//     * @return AnonymousResourceCollection|string
     */
    public function getFabricPictureSchemas()
    {


        try {
            return FabricPictureSchemaResource::collection(FabricPictureSchema::all());
        } catch (\Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }
    }
}
