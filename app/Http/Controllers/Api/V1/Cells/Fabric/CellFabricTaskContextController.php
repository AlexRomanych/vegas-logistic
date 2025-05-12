<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskContextCollection;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class CellFabricTaskContextController extends Controller
{


    /**
     * Descr: Удаляем контекст СЗ по id
     * @param Request $request
     * @return string
     */
    public function deleteContext(Request $request)
    {
        try {

            if (!$request->has('id')) return EndPointStaticRequestAnswer::fail();

            $validData = $request->validate([
                'id' => 'numeric|required',
            ]);

            $taskContext = FabricTaskContext::query()->find($validData['id']);

            if ($taskContext) $taskContext->delete();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * Descr: Получаем контекст СЗ (то, что выставлено ООП) со всеми статусами, кроме 'выполнено'
     * @return FabricTaskContextCollection|string
     */
    public function getContextNotDone()
    {
        try {

            $taskContexts = FabricTaskContext::query()
                ->whereHas('fabricTask', function ($query) {
                    $query->whereNot('task_status', FABRIC_TASK_DONE_CODE);
                })
                ->with('fabricTask')
                ->get();

            return new FabricTaskContextCollection($taskContexts);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    public function createContextExpense(Request $request)
    {

        $result = $request->validate([
            'data.data' => 'required|array',
            'data.data.date' => 'nullable|date_format:Y-m-d',
            'data.data.fabric_id' => 'required|numeric',
            'data.data.fabric_delta' => 'required|numeric'
        ]);

        $contextExpenseData = $request->input('data')['data'];

        $contextDate = $contextExpenseData['date'] ?? (Carbon::now())->format('Y-m-d');






//        return ['qqq' => $delta];

//        $request->validate([
//            'date' => 'nullable|date_format:Y-m-d',
//            'data.data.fabric_delta' => 'required|numeric',
//            'data.fabric_id' => 'required|numeric',
//        ]);

//        return ['qqq' => $request->input('data')['data']];
    }


}
