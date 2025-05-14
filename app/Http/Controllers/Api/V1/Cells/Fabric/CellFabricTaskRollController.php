<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollMovingCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollResource;

//use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\Fabric;
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
     * Descr: Возвращает список не принятых на закрой рулонов (DONE или REGISTERED_1C или MOVED)
     * @param Request $request
     * @return FabricTaskRollMovingCollection
     */
    public function getNotAcceptedToCutRolls(Request $request)
    {
        $rollsQuery = FabricTaskRoll::query()
            ->where('roll_status', FABRIC_ROLL_DONE_CODE)
            ->orWhere('roll_status', FABRIC_ROLL_REGISTERED_1C_CODE)
            ->orWhere('roll_status', FABRIC_ROLL_MOVED_CODE)
            ->with([
                'fabric',
                'finishBy',
                'registration1CBy',
                'moveToCutBy',
                'receiptToCutBy',
                'user'])
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

    /**
     * Descr: Задает статус рулона как зарегистрированный в 1С
     * @param Request $request
     * @return string
     */
    public function setRollRegisteredStatus(Request $request)
    {
        try {
            $result = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|numeric',
                'data.status' => 'required|numeric'
            ]);

            $status = $result['data']['status'];
            if ($status !== FABRIC_ROLL_REGISTERED_1C_CODE && $status !== FABRIC_ROLL_DONE_CODE) {
                throw new Exception('Неизвестный статус');
            }

            $roll = FabricTaskRoll::query()->find($result['data']['id']);

            if (!$roll) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Рулон не найден']));
            }

            if ($roll->roll_status !== FABRIC_ROLL_REGISTERED_1C_CODE && $roll->roll_status !== FABRIC_ROLL_DONE_CODE) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Статус не соответствует действительности']));
            }

            if ($status === FABRIC_ROLL_REGISTERED_1C_CODE) {
                $result = $roll->update([
                    'roll_status' => FABRIC_ROLL_REGISTERED_1C_CODE,
                    'registration_1C_by' => Auth::id(),
                    'registration_1C_at' => now(),
                ]);
            } else {
                $result = $roll->update([
                    'roll_status' => FABRIC_ROLL_DONE_CODE,
                    'registration_1C_by' => 0,
                    'registration_1C_at' => null,
                ]);
            }

            if ($result) throw new Exception('Не удалось обновить статус');

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    /**
     * Descr: Задает статус рулона как перемещенный на закрой
     * @param Request $request
     * @return string
     */
    public function setRollMovedStatus(Request $request)
    {
        try {
            $result = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|numeric',
                'data.status' => 'required|numeric'
            ]);

            $status = $result['data']['status'];
            if ($status !== FABRIC_ROLL_REGISTERED_1C_CODE && $status !== FABRIC_ROLL_MOVED_CODE) {
                throw new Exception('Неизвестный статус');
            }

            $roll = FabricTaskRoll::query()->find($result['data']['id']);

            if (!$roll) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Рулон не найден']));
            }

            if ($roll->roll_status !== FABRIC_ROLL_REGISTERED_1C_CODE && $roll->roll_status !== FABRIC_ROLL_MOVED_CODE) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Статус не соответствует действительности']));
            }

            $delta = $roll->textile_roll_length / $roll->translate_rate; // +/- количество к буферу
            if ($status === FABRIC_ROLL_MOVED_CODE) {
                $result = $roll->update([
                    'roll_status' => FABRIC_ROLL_MOVED_CODE,
                    'move_to_cut_by' => Auth::id(),
                    'move_to_cut_at' => now(),
                ]);

            } else {
                $result = $roll->update([
                    'roll_status' => FABRIC_ROLL_REGISTERED_1C_CODE,
                    'move_to_cut_by' => 0,
                    'move_to_cut_at' => null,
                ]);
                $delta = -$delta;
            }

            if (!$result) throw new Exception('Не удалось обновить статус');

            // attract: Изменяем количество в буфере ткани в зависимости от статуса
            $fabric = Fabric::query()->find($roll->fabric_id);

            if (!$fabric) throw new Exception('Не удалось найти ткань');

            $fabric->buffer_amount += $delta;
            $fabric->save();

            return EndPointStaticRequestAnswer::ok();
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
