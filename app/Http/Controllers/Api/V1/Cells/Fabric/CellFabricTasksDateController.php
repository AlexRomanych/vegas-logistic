<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTasksDateCollection;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use App\Services\Manufacture\FabricService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class CellFabricTasksDateController extends Controller
{
    /**
     * Возвращает все заказы за период
     * Если без параметров, то все заказы
     * @param Request $request
     * @return FabricTaskCollection|FabricTasksDateCollection|string
     */
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
//                ->with(['fabricTasks.fabricTaskContexts', 'fabricTasks.fabricTaskRolls'])
                ->with(['fabricTasks.fabricTaskContexts.fabricTaskRolls'])
                ->get();

            return new FabricTasksDateCollection($tasksQuery);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }

    /**
     * Создает или обновляет данные по сменным заданиям на конкретную дату
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {

        try {
            $payload = $request->all();
            $tasksDayData = $payload['data']['data'];               // одна data в axios, вторая data в самом объекте task

//        return $tasksDayData;

            // todo: сделать валидацию данных

            // Проверка статуса задания (если 0 - пропускаем)
            if ($tasksDayData['common']['status'] === FABRIC_TASK_UNKNOWN_CODE) {
                return EndPointStaticRequestAnswer::ok();
            }

            // Перебираем все машины
            for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {

                // Получаем название машины
                $machineStr = FabricService::getFabricMachineNameById($i);

                // Проверка на наличие данных для данной машины
                if (count($tasksDayData['machines'][$machineStr]['rolls']) === 0) continue;

                // Создаем задание или обновляем основное задание на эту дату
                $tasksDay = $this->createOrUpdateTasksDate($tasksDayData);

                // Выносим для меньшей писанины в отдельную переменную
                $taskData = $tasksDayData['machines'][$machineStr];

                // если создали и обновили успешно, то получили экземпляр даты задания
                // создаем тогда FabricTask - СЗ для СМ
                if ($tasksDay instanceof Model) {

                    // Создаем саму сущность СЗ, к которому привязываем задание от ОПП и рулоны
                    $task = FabricTask::query()->updateOrCreate(
                        [
                            'fabric_tasks_date_id' => $tasksDay->id,
                            'fabric_machine_id' => $i,
                        ],
                        [
                            'fabric_tasks_date_id' => $tasksDay->id,
                            'fabric_machine_id' => $i,
                            'task_status' => $tasksDay->tasks_status,             // записываем пока общий статус всего СЗ
                            'active' => $taskData['active'],
                            'task_finish_at' => $taskData['finish_at'],
                            'description' => $taskData['description'],
                        ]
                    );

                    // Создаем или обновляем все рулоны, которые пришли с бэка
                    foreach ($tasksDayData['machines'][$machineStr]['rolls'] as $key => $rollData) {

                        // плановый рулон от ОПП
                        $taskRollContext = FabricTaskContext::query()->updateOrCreate(
                            [
                                'fabric_task_id' => $task->id,
                                'fabric_id' => $rollData['fabric_id']
                            ],
                            [
                                'roll_position' => $key + 1,
                                'fabric_task_id' => $task->id,                          // привязка к СЗ
                                'fabric_id' => $rollData['fabric_id'],                  // привязка к ПС
                                'fabric_mode' => $rollData['fabric_mode'],
                                'rolls_amount' => $rollData['rolls_amount'],
                                'average_textile_length' => $rollData['average_textile_length'],
                                'productivity' => $rollData['productivity'],
                                'description' => $rollData['descr'],
                            ]
                        );
                    }
                }
            }

//        return 'reached';
            return EndPointStaticRequestAnswer::ok();

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * descr: Изменение статуса задания
     * @param Request $request
     * @return string
     */
    public function statusChange(Request $request)
    {
        try {

            $payload = $request->all();
            $tasksDayData = $payload['data']['data'];               // одна data в axios, вторая data в самом объекте task

            // todo: сделать валидацию данных
            // todo: сделать проверку при изменении статуса на "Готов к стежке". Должно быть задание хотя бы на одну СМ

            $tasksDate = $this->createOrUpdateTasksDate($tasksDayData);

            // Если создали и обновили успешно, то получили экземпляр даты задания
            if ($tasksDate instanceof Model) {

                // attract: Если статус "Не создано", то удаляем
                if ($tasksDayData['common']['status'] === FABRIC_TASK_UNKNOWN_CODE) {

                    $tasksDate->delete();

                    return EndPointStaticRequestAnswer::ok();
                }

                // attract: Если движение статуса от "Создано" на "Готов к стежке",
                // attract: то запускаем формирование рулона по каждому ПС
                // attract: Если движение статуса от "Готов к стежке" на "Создано",
                // attract: то запускаем удаление всех рулонов по каждому ПС
                if ($tasksDayData['common']['status'] === FABRIC_TASK_CREATED_CODE ||
                    $tasksDayData['common']['status'] === FABRIC_TASK_PENDING_CODE) {

                    // Задаем режим работы.
                    // Если "Создано" -> "Готов к стежке" (True)  - запускаем формирование рулона по каждому ПС,
                    // если "Готов к стежке" -> "Создано" (False) - запускаем удаление всех рулонов по каждому ПС
                    $mode = $tasksDayData['common']['status'] === FABRIC_TASK_PENDING_CODE;

                    for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {

                        // Получаем название машины
                        $machineStr = FabricService::getFabricMachineNameById($i);

                        // Проверка на наличие данных для данной стегальной машины
                        if (count($tasksDayData['machines'][$machineStr]['rolls']) === 0) continue;

                        // Получаем само СЗ
                        $task = FabricTask::query()
                            ->where('fabric_tasks_date_id', $tasksDate->id)
                            ->where('fabric_machine_id', $i)
                            ->with('fabricTaskContexts')
                            ->get()
                            ->toArray();    // warning: Приходит Collection, но вложенные массивы не работают

                        // Создаем  все физические рулоны, на основании плановых
                        foreach ($task[0]['fabric_task_contexts'] as $key => $taskContext) {

                            // attract: Создаем  все физические рулоны, на основании плановых, или удаляем их
                            if ($mode) {

                                for ($j = 0; $j < $taskContext['rolls_amount']; $j++) {
                                    $roll = FabricTaskRoll::query()->create(
                                        [
                                            'fabric_task_context_id' => $taskContext['id'],
                                            'fabric_id' => $taskContext['fabric_id'],
                                            'roll_position' => $j + 1,
                                            'roll_status' => FABRIC_ROLL_UNREADY,
                                            'textile_roll_length' => $taskContext['average_textile_length'],
                                            'productivity' => $taskContext['productivity'],
                                            'description' => $taskContext['description'],   // дописываем плановый комментарий
                                        ]
                                    );
                                }

                            } else {

                                FabricTaskRoll::query()
                                    ->where('fabric_task_context_id', $taskContext['id'])
                                    ->delete();

                            }
                        }

                    }

                    return 'reached__';
                }

            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * Удаляем контекст СЗ по id
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
     * Возвращаем результат создания или обновления дня заданий
     * @param $tasksDayData
     * @return Model|null
     */
    private function createOrUpdateTasksDate($tasksDayData)
    {
        if (is_null($tasksDayData)) return null;

        // Возвращаем результат создания или обновления дня заданий
        return FabricTasksDate::query()->updateOrCreate(
            [
                'tasks_date' => $tasksDayData['date'],
            ],
            [
                'tasks_date' => $tasksDayData['date'],
                'tasks_status' => $tasksDayData['common']['status'],
                'description' => $tasksDayData['common']['description'],

                // todo: сделать проверку на существование бригады и вообще тут продумать сущность
                'fabric_team_id' => FabricService::getFabricTeamChangeNumberByDate($tasksDayData['date']),
                'active' => $tasksDayData['active'],
            ]
        );

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
