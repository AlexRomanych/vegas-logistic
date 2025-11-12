<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskCollection;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
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
                'end'   => 'required|date|afterOrEqual:start',
            ]);

            $tasksQuery = FabricTask::query()
                ->whereBetween('task_date', [$validData['start'], $validData['end']])
                //                ->with(['fabricTaskContexts', 'fabricTaskRolls'])
                ->with(['fabricTaskContexts'])
                ->get();

            return json_encode($tasksQuery);

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
                    'date'   => $taskDate,
                    'common' => [
                        [
                            'active'      => true,
                            'status'      => 0,
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
                    'task_date'         => $taskData['date'],
                    'fabric_machine_id' => $i
                ],
                [
                    'task_date'         => $taskData['date'],
                    'fabric_machine_id' => $i,
                    'task_status'       => $taskData['common']['status'],
                    'description'       => $taskData['common']['description'],

                    // todo: сделать проверку на существование бригады и вообще тут продумать сущность
                    'fabric_team_id'    => FabricService::getFabricTeamChangeNumberByDate($taskData['date']),
                    //                        'fabric_team_id' => $taskData['common']['team'],
                    'active'            => true,
                    //                        'active' => $taskData['active'],
                ]
            );

            // Создаем или обновляем все рулоны, которые пришли с бэка
            foreach ($taskData['machines'][$machineStr]['rolls'] as $key => $rollData) {

                //                    return $rollData;

                $fabricTaskRoll = FabricTaskContext::query()->updateOrCreate(
                    [
                        'fabric_task_id' => $task->id,
                        'fabric_id'      => $rollData['fabric_id']
                    ],
                    [
                        'roll_position'  => $key + 1,
                        'fabric_mode'    => $rollData['fabric_mode'],
                        'rolls_amount'   => $rollData['rolls_amount'],
                        'textile_length' => $rollData['textile_length'],
                        'description'    => $rollData['descr'],
                        'fabric_id'      => $rollData['fabric_id'],                  // привязка к ПС
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
                'task_date'      => $taskData['date'],
                //                'fabric_machine_id' => $i,
                'task_status'    => $taskData['common']['status'],
                'description'    => $taskData['common']['description'],

                // todo: сделать проверку на существование бригады и вообще тут продумать сущность
                'fabric_team_id' => FabricService::getFabricTeamChangeNumberByDate($taskData['date']),
                'active'         => $taskData['active'],
            ]
        );


        return $taskData;
    }

    /**
     * ___ Обновляем комментарий к СЗ к данной СМ
     * @param Request $request
     * @return string
     */
    public function updateTaskComment(Request $request)
    {
        try {
            $all = $request->all();

            $validatedData = $request->validate([
                'data'             => 'required|array',
                'data.task'        => 'required|integer|min:1',
                'data.machine'     => 'required|integer|min:1',
                'data.description' => 'nullable|string'
            ]);

            $data = $validatedData['data'];

            $task = FabricTask::query()
                ->where('fabric_tasks_date_id', $data['task'])
                ->where('fabric_machine_id', $data['machine'])
                ->first();

            if (!$task) {
                throw new Exception('Task not found');
            };

            $description = $data['description'] ?? '';

            $task->description = $description;
            $task->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
