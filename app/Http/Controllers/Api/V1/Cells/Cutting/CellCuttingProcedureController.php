<?php

namespace App\Http\Controllers\Api\V1\Cells\Cutting;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Cutting\Procedures\CuttingProcedureResource;
use App\Models\Manufacture\Cells\Cutting\CuttingProcedure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class CellCuttingProcedureController extends Controller
{

    /**
     * ___ Возвращаем Процедуры Раскроя
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingProcedures()
    {
        try {
            $cuttingProcedures = CuttingProcedure::all();
            return CuttingProcedureResource::collection($cuttingProcedures);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Получаем Процедуру Раскроя по id
     * @param string $id
     * @return CuttingProcedureResource|string
     */
    public function getCuttingProcedure(string $id)
    {
        try {
            $validator = Validator::make(['id' => $id], ['id' => 'required|numeric|exists:cutting_procedures,id']);
            $validator->validate(); // __Пробрасываем исключение

            $cuttingProcedure = CuttingProcedure::query()->findOrFail($id);
            return new CuttingProcedureResource($cuttingProcedure);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
