<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollMovingCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollResource;

//use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\Auth;

//use Carbon\Carbon;
//use Illuminate\Support\Facades\DB;

class CellFabricTaskRollController extends Controller
{

    /**
     * Descr: Возвращает рулоны за период. Если не указан - возвращает все рулоны
     * @param Request $request
     * @return FabricTaskRollCollection|string
     */
    public function taskRolls(Request $request)
    {
        try {

            // Если без параметров, возвращаем все заказы
            if (!$request->has('start') || !$request->has('end')) {
                return new FabricTaskRollCollection(FabricTaskRoll::all());
            }

            $validData = $request->validate([
                'start' => 'required|date|beforeOrEqual:end',
                'end' => 'required|date|afterOrEqual:start',
            ]);

            $rollsQuery = FabricTaskRoll::query()
                ->whereBetween('created_at', [$validData['start'], $validData['end']])
                ->orderBy('id')
                ->get();

            return new FabricTaskRollCollection($rollsQuery);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * Descr: Возвращает список выполненных рулонов
     * @param Request $request
     * @return FabricTaskRollMovingCollection
     */
    public function getDoneRolls(Request $request)
    {
        $rollsQuery = FabricTaskRoll::query()
            ->where('roll_status', FABRIC_ROLL_DONE_CODE)
            ->with(['fabric', 'finishBy', 'moveToCutBy', 'receiptToCutBy', 'user'])
//            ->with(['fabric', 'finishBy', 'registration1CBy', 'moveToCutBy', 'receiptToCutBy', 'user'])     // warning:
            ->orderBy('id')
            ->get();

        return new FabricTaskRollMovingCollection($rollsQuery);
    }




    /**
     * Descr: Обновляет данные по рулону.
     * @param Request $request
     * @return FabricTaskRollResource|string
     */
    public function update(Request $request)
    {
        try {

            $rollData = $request->data['data'];

//            return $rollData;

            // TODO: Выполнить жесткую валидацию жестких данных

            $updateData = [
                'roll_status' => $rollData['status'],
                'roll_status_previous' => $rollData['status_prev'],
                'start_at' => $this->getUTCDateOrNull($rollData['start_at']),
                'paused_at' => $this->getUTCDateOrNull($rollData['paused_at']),
                'resume_at' => $this->getUTCDateOrNull($rollData['resume_at']),
                'finish_at' => $this->getUTCDateOrNull($rollData['finish_at']),
                'duration' => $rollData['duration'],
                'finish_by' => $rollData['finish_by'],
                'rolling' => $rollData['rolling'],
                'description' => $rollData['descr'],
                'false_reason' => $rollData['false_reason'],
                'textile_roll_length' => $rollData['textile_length'],
                'user_id' => Auth::id(),    // Текущий пользователь
            ];


            $taskRoll = FabricTaskRoll::query()->updateOrCreate(
                ['id' => $rollData['id']],
                $updateData

            );

            return new FabricTaskRollResource($taskRoll);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    // attract: Вспомогалочка. Или Null, или правильная дата (+3 часа)
    private function getUTCDateOrNull($date)
    {
        if (is_null($date)) return null;
        else return correctTimeZone($date);
    }

}

/*
descr: Это поля объекта выполняемого рулона
{
    "id": 23,
    "fabric_id": 9,
    "position": 2,
    "status": 4,
    "start_at": null,
    "paused_at": null,
    "resume_at": null,
    "finish_at": null,
    "duration": null,
    "finish_by": 0,
    "rolling": false,
    "textile_length": 68.63,
    "productivity": 47.61904762,
    "descr": null,
    "active": true
}
*/
