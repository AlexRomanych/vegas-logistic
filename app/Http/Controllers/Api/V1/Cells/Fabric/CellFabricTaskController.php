<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskCollection;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Services\Manufacture\FabricService;
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
                return new FabricTaskCollection(FabricTask::all());
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


    public function create(Request $request)
    {
//        try {
            $payload = $request->all();
            $taskData = $payload['data']['data'];               // одна data в axios, вторая data в самом объекте task

            // todo: сделать валидацию данных

            // Проверка статуса задания (если 0 - пропускаем)
            if ($taskData['common']['status'] === FABRIC_TASK_UNKNOWN_CODE) {

//                return $taskData['common']['status'];
                return EndPointStaticRequestAnswer::ok();
            }

            // Перебираем все машины
            for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {

                // Получаем название машины
                $machineStr = FabricService::getFabricMachineNameById($i);

//                return $payload;
//                return $payload['machines'][$machineStr];
                // Проверка на наличие данных для данной машины
                if (count($taskData['machines'][$machineStr]['rolls']) === 0) continue;

                // Создаем задание или обновляем основное задание
                $task = FabricTask::query()->updateOrCreate(
                    [
                        'task_date' => $taskData['date'],
                        'fabric_machine_id' => $i
                    ],
                    [
                        'task_date' => $taskData['date'],
                        'fabric_machine_id' => $i,
                        'task_status' => $taskData['common']['status'],
                        'description' => $taskData['common']['description'],
                        'fabric_team_id' => FabricService::getFabricTeamChangeNumberByDate($taskData['date']),
//                        'fabric_team_id' => $taskData['common']['team'],
                        'active' => true,
//                        'active' => $taskData['active'],
                    ]
                );



            }


            return EndPointStaticRequestAnswer::ok();

//        } catch (Exception $e) {
//            return EndPointStaticRequestAnswer::fail(response()->json($e));
//        }
//        return $request->all();
    }


}

/*
{
    "date": "2025-04-02",
    "active": true,
    "common": {
        "team": 1,
        "status": 4
        "description": "description"
    },
    "machines": {
    "american": {
        "rolls": [
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 38001,
                    "fabric_id": 7,
                    "fabric": "ПС 218Ж 100С 15С блэк (рис. Ж2)",
                    "rolls_amount": 1,
                    "length_amount": 150.5,
                    "descr": "I am description 1"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 49002,
                    "fabric_id": 9,
                    "fabric": "ПС 220Ж 100С 15С М-24 (рис. К2)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description 2"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 51003,
                    "fabric_id": 11,
                    "fabric": "ПС 220Ж 100С 220Ж микрофибра (рис. Ж2)",
                    "rolls_amount": 3,
                    "length_amount": 150.5,
                    "descr": "I am description 3"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 51004,
                    "fabric_id": 12,
                    "fabric": "ПС 220Ж 200С 220Ж микрофибра (рис. Ж2)",
                    "rolls_amount": 4,
                    "length_amount": 150.5,
                    "descr": "I am description 4"
                },
                {
                    "textile_length": 36.76965318,
                    "fabric_id": 101,
                    "fabric_name": "ПС 230Т 200С 30С стар (рис. Е1)",
                    "rolls_amount": 1,
                    "descr": null,
                    "fabric_mode": true
                }
            ]
        },
        "german": {
        "rolls": [
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 28004,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 28005,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 28006,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                }
            ]
        },
        "china": {
        "rolls": [
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 29007,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 29008,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 29009,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                }
            ]
        },
        "korean": {
        "rolls": [
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 31001,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 32002,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                },
                {
                    "average_length": 59.9,
                    "roll_id": 0,
                    "num": 33003,
                    "fabric_id": 0,
                    "fabric": "ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)",
                    "rolls_amount": 2,
                    "length_amount": 150.5,
                    "descr": "I am description"
                }
            ]
        }
    }
}

*/
