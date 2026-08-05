<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Assembly\ManufactureGroups\AssemblyModelManufactureGroupResource;
use App\Models\Models\ModelManufactureGroup;
use App\Services\Manufacture\AssemblyService;
use Exception;
use Illuminate\Http\Request;

class AssemblyController extends Controller
{

    /*
     * ___ Получаем Группы Моделей для Сортировки
     */
    public function getModelManufactureGroups()
    {
        try {
            $modelManufactureGroups = ModelManufactureGroup::all();
            return AssemblyModelManufactureGroupResource::collection($modelManufactureGroups);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }











    public function test(Request $request)
    {
        $result = AssemblyService::test();
        return $result;
    }
}
