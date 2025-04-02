<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskCollection;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use Exception;
use Illuminate\Http\Request;

class CellFabricTaskController extends Controller
{
    public function tasks(Request $request)
    {
//        return json_encode($request->all());
        try {

            // Если без параметров, возвращаем все заказы
            if (!$request->has('start') || !$request->has('end')) {
                return FabricTask::all();
            }

            $validData = $request->validate([
                'start' => 'required|date|beforeOrEqual:end',
                'end' => 'required|date|afterOrEqual:start',
            ]);

            $tasks = FabricTask::query()
                ->whereBetween('task_date', [$validData['start'], $validData['end']])
                ->with(['fabricTaskRolls'])
                ->get();

            return new FabricTaskCollection($tasks);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }





}
