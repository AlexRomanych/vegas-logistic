<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTasksDateCollection;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use App\Services\Manufacture\FabricService;
use Exception;
use Illuminate\Http\Request;


class CellFabricTasksDateController extends Controller
{
    public function tasks(Request $request)
    {



//        return json_encode($request->all());
        try {

            // Если без параметров, возвращаем все заказы
            if (!$request->has('start') || !$request->has('end')) {
                return new FabricTaskCollection(FabricTasksDate::all());
            }

            $validData = $request->validate([
                'start' => 'required|date|beforeOrEqual:end',
                'end' => 'required|date|afterOrEqual:start',
            ]);

            $tasksQuery = FabricTasksDate::query()
                ->whereBetween('tasks_date', [$validData['start'], $validData['end']])
                // relations добавляем основные + вложенные
                ->with(['fabricTasks.fabricTaskContexts', 'fabricTasks.fabricTaskRolls'])
                ->get();

            return new FabricTasksDateCollection($tasksQuery);

//            return json_encode($tasksQuery);

//            $tasksArray = $tasks->toArray();

//            $tasksUniqueDatesArray = $task    s->

            // Преобразуем коллекцию в массив
            $tasksArray = json_decode(json_encode($tasksQuery), true);

            // attract: Получаем сначала массив с уникальными датами
            $tasksDatesArray = [];
            foreach ($tasksArray as $key => $task) {
                $tasksDatesArray[] = $task['task_date'];
            }
            $tasksDatesArrayUnique = array_unique($tasksDatesArray);

            // attract: Формируем выходной массив
            $tasks = [];
            foreach ($tasksDatesArrayUnique as $key => $taskDate) {

                $tempDateMachineTask = [
                    'date' => $taskDate,
                    'common' => [
                        [
                            'active' => true,
                            'status' => 0,
                            'description' => ''
                        ]
                    ]
                ];





                for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {


                    // Получаем название машины
                    $machineStr = FabricService::getFabricMachineNameById($i);
                }
            }


            return $tasksDatesArray;                                // массив с уникальными датами

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

                    // todo: сделать проверку на существование бригады и вообще тут продумать сущность
                    'fabric_team_id' => FabricService::getFabricTeamChangeNumberByDate($taskData['date']),
//                        'fabric_team_id' => $taskData['common']['team'],
                    'active' => true,
//                        'active' => $taskData['active'],
                ]
            );

            // Создаем или обновляем все рулоны, которые пришли с бэка
            foreach ($taskData['machines'][$machineStr]['rolls'] as $key => $rollData) {

//                    return $rollData;

                $fabricTaskRoll = FabricTaskContext::query()->updateOrCreate(
                    [
                        'fabric_task_id' => $task->id,
                        'fabric_id' => $rollData['fabric_id']
                    ],
                    [
                        'roll_position' => $key + 1,
                        'fabric_mode' => $rollData['fabric_mode'],
                        'rolls_amount' => $rollData['rolls_amount'],
                        'textile_length' => $rollData['textile_length'],
                        'description' => $rollData['descr'],
                        'fabric_id' => $rollData['fabric_id'],                  // привязка к ПС
                        'fabric_task_id' => $task->id                          // привязка к СЗ
                    ]
                );

            }

        }


        return EndPointStaticRequestAnswer::ok();

//        } catch (Exception $e) {
//            return EndPointStaticRequestAnswer::fail(response()->json($e));
//        }
//        return $request->all();
    }


    public function statusChange(Request $request)
    {
        $payload = $request->all();
        $taskData = $payload['data']['data'];               // одна data в axios, вторая data в самом объекте task

        // todo: сделать валидацию данных

        // Проверка статуса задания (если 0 - пропускаем)
        if ($taskData['common']['status'] === FABRIC_TASK_UNKNOWN_CODE) {
            return EndPointStaticRequestAnswer::ok();
        }

        $task = FabricTask::query()->updateOrCreate(
            [
                'task_date' => $taskData['date'],
//                'fabric_machine_id' => $i
            ],
            [
                'task_date' => $taskData['date'],
//                'fabric_machine_id' => $i,
                'task_status' => $taskData['common']['status'],
                'description' => $taskData['common']['description'],

                // todo: сделать проверку на существование бригады и вообще тут продумать сущность
                'fabric_team_id' => FabricService::getFabricTeamChangeNumberByDate($taskData['date']),
                'active' => $taskData['active'],
            ]
        );


        return $taskData;
    }

}


/*
{
    "date": "2025-04-02",
    "active": true,
    "common": {
        "team": 1,
        "status": 4

    },
    "machines": {
    "american": {
        "description": "description"
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
                    "fabric_id": 101,
                    "fabric_name": "ПС 230Т 200С 30С стар (рис. Е1)",
                    "rolls_amount": 1,
                    "descr": null,
                    "textile_length": 36.76965318,
                    "fabric_mode": true
                }
            ]
        },
        "german": {
            "description": "description"
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
            "description": "description"
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
            "description": "description"
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
