<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollResource;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\DB;

class CellFabricTaskRollController extends Controller
{
    public function update(Request $request)
    {
        try {

            $rollData = $request->data['data'];

//            return $rollData;

            // TODO: Выполнить жесткую валидацию жестких данных

            $updateData = [
                'roll_status' => $rollData['status'],
                'start_at' => $this->getUTCDateOrNull($rollData['start_at']),
                'paused_at' => $this->getUTCDateOrNull($rollData['paused_at']),
                'resume_at' => $this->getUTCDateOrNull($rollData['resume_at']),
                'finish_at' => $this->getUTCDateOrNull($rollData['finish_at']),
                'duration' => $rollData['duration'],
                'finish_by' => $rollData['finish_by'],
                'rolling' => $rollData['rolling'],
                'description' => $rollData['descr'],
            ];

//        if (!is_null($rollData['start_at'])) $updateData['start_at'] = $rollData['start_at'];
//        if (!is_null($rollData['status'])) {}

            $taskRoll = FabricTaskRoll::query()->updateOrCreate(
                ['id' => $rollData['id']],
                $updateData

            );

//            return json_encode(['raw' => DB::select('SELECT CURRENT_TIMESTAMP')]);

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
