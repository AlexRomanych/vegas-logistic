<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reason\CellItemResource;
use App\Models\Manufacture\CellsGroup;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

//use App\Http\Resources\Reason\ReasonResource;
//use Illuminate\Http\Request;

class ReasonController extends Controller
{
    /**
     * ___ Получаем список причин
     * @return AnonymousResourceCollection|string
     */
    public function reasons()
    {
        try {
            return CellItemResource::collection(CellsGroup::query()->with('reasonCategories.reasons')->get());
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
