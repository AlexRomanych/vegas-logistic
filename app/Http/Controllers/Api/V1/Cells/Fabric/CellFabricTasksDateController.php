<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTasksDateCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTasksDateResource;
use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use App\Models\Worker\WorkerRecord;
use App\Services\Manufacture\FabricService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

//use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskCollection;
//use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollResource;

class CellFabricTasksDateController extends Controller
{

    /**
     *   ___ Возвращает все заказы за период
     *   ___ Если без параметров, то все заказы
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function tasks(Request $request)
    {
        // TODO: сделать валидацию

        try {
            // Если без параметров, возвращаем все заказы
            if (!$request->has('start') || !$request->has('end')) {
                return FabricTasksDateResource::collection(FabricTasksDate::all());
            }

            $validData = $request->validate([
                'start' => 'required|date|beforeOrEqual:end',
                'end' => 'required|date|afterOrEqual:start',
            ]);

            $tasksQuery = FabricTasksDate::query()
                ->whereBetween('tasks_date', [$validData['start'], $validData['end']])
                // relations добавляем основные + вложенные + user
                ->with([
                    'fabricTasks.fabricTaskContexts.fabricTaskRolls',
                    'fabricTasks.fabricTaskContexts.fabric',
                    'team',
                    'user',
                    'workerRecord', 'workerRecord.worker',
                    'fabricTasks.fabricTaskContexts.fabricTaskRolls.finishBy',
                    'fabricTasks.fabricTaskContexts.fabricTaskRolls.registration1CBy',
                    'fabricTasks.fabricTaskContexts.fabricTaskRolls.moveToCutBy',
                    'fabricTasks.fabricTaskContexts.fabricTaskRolls.receiptToCutBy',
                ])
                ->orderBy('tasks_date')
                ->get();

            return FabricTasksDateResource::collection($tasksQuery);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    /**
     * ___ Создает или обновляет данные по сменным заданиям на конкретную дату
     * @return string
     * @throws Throwable
     */
    public function create(Request $request)
    {

        try {

            // TODO: сделать валидацию данных
            $payload = $request->all();
            $tasksDayData = $payload['data']['data']; // одна data в axios, вторая data в самом объекте task

            // return $tasksDayData;

            // Проверка статуса задания (если 0 - пропускаем)
            if ($tasksDayData['common']['status'] === FABRIC_TASK_UNKNOWN_CODE) {
                return EndPointStaticRequestAnswer::ok();
            }

            $debug = $tasksDayData;

            // __ Начинаем транзакцию
            DB::transaction(function () use ($tasksDayData) {

                // __ Создаем задание или обновляем основное задание на эту дату
                $tasksDay = $this->createOrUpdateTasksDate($tasksDayData);

                // $ret = [];

                // __ Перебираем все машины
                for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {

                    // __ Получаем название машины
                    $machineStr = FabricService::getFabricMachineNameById($i);

                    // $ret[] = [$machineStr => $rollData['roll_position']];

                    // __ Проверка на наличие данных для данной машины
                    if (count($tasksDayData['machines'][$machineStr]['rolls']) === 0) continue;

                    // __ Выносим для меньшей писанины в отдельную переменную
                    $taskData = $tasksDayData['machines'][$machineStr];

                    // __ Если создали и обновили успешно, то получили экземпляр даты задания
                    // __ создаем тогда FabricTask - СЗ для СМ
                    if ($tasksDay instanceof Model) {

                        // __ Создаем саму сущность СЗ, к которому привязываем задание от ОПП и рулоны
                        $task = FabricTask::query()->updateOrCreate(
                            [
                                'fabric_tasks_date_id' => $tasksDay->id,
                                'fabric_machine_id' => $i,
                            ],
                            [
                                'fabric_tasks_date_id' => $tasksDay->id,
                                'fabric_machine_id' => $i,
                                'task_status' => $tasksDay->tasks_status,             // записываем пока общий статус всего СЗ
                                'fabric_team_id' => $tasksDay['fabric_team_id'],
                                'active' => $taskData['active'],
                                'task_finish_at' => $taskData['finish_at'],
                                'description' => $taskData['description'],
                            ]
                        );

                        // __ Создаем или обновляем все рулоны, которые пришли с бэка
                        foreach ($tasksDayData['machines'][$machineStr]['rolls'] as $key => $rollData) {


                            // $ret[] = [$machineStr => $rollData['roll_position']];
                            // return    ['rollData' => $rollData['roll_position']];

                            // __ плановый рулон от ОПП
                            $taskRollContext = FabricTaskContext::query()->updateOrCreate(
                                [
                                    'fabric_task_id' => $task->id,
                                    'fabric_id' => $rollData['fabric_id'],
                                    'editable' => true,                                     // касается только не переходящих рулонов
                                ],
                                [
                                    // 'roll_position' => $key + 1,
                                    'roll_position' => $rollData['roll_position'],          // порядок рулонов
                                    'fabric_task_id' => $task->id,                          // привязка к СЗ
                                    'fabric_id' => $rollData['fabric_id'],                  // привязка к ПС
                                    'fabric_mode' => $rollData['fabric_mode'],
                                    'rolls_amount' => $rollData['rolls_amount'],
                                    'average_textile_length' => $rollData['average_textile_length'],
                                    'translate_rate' => $rollData['rate'],
                                    'productivity' => $rollData['productivity'],
                                    'description' => $rollData['descr'],
                                ]
                            );
                        }
                    }
                }

            });

            return EndPointStaticRequestAnswer::ok();
            // return json_encode($ret);
            // return 'reached';

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
//        try {

            $payload = $request->all();
            $tasksDayData = $payload['data']['data'];               // одна data в axios, вторая data в самом объекте task

            // todo: сделать валидацию данных
            // todo: сделать проверку при изменении статуса на "Готов к стежке". Должно быть задание хотя бы на одну СМ

            // attract: Если статус от "Готов к стежке" на "Выполняется"
            // attract: Проверяем наличие running СЗ
            // attract: и проверяем что СЗ до этой даты имеют статус "Выполнено"
            if ($tasksDayData['common']['status'] === FABRIC_TASK_RUNNING_CODE) {

                if (count($this->getFabricExecutingTasks(new Request(['date' => $tasksDayData['date']]))) !== 0) {
                    throw new Exception('Есть выполняющиеся задания');
//                    return EndPointStaticRequestAnswer::fail('Есть выполняющиеся задания');
                }

                if (count($this->getFabricNotDoneTasks(new Request(['date' => $tasksDayData['date']]))) !== 0) {
                    throw new Exception('Есть задания с непонятным статусом');
//                    return EndPointStaticRequestAnswer::fail('Есть задания с непонятным статусом');
                }

            }

            // attract: Манипулируем со СЗ
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

                        // __ Получаем само СЗ
                        $task = FabricTask::query()
                            ->where('fabric_tasks_date_id', $tasksDate->id)
                            ->where('fabric_machine_id', $i)
                            ->with('fabricTaskContexts')
                            ->get()
                            ->toArray();    // warning: Приходит Collection, но вложенные массивы не работают

                        // __ Сортируем по порядку
                        uasort($task[0]['fabric_task_contexts'], function ($a, $b) {
                            return $a['roll_position'] <=> $b['roll_position'];
                        });

//                         return ['task' => $task[0]['fabric_task_contexts']];

                        // __ Создаем  все физические рулоны, на основании плановых
                        $count = EXEC_ROLL_START_ORDER_INDEX;
                        foreach ($task[0]['fabric_task_contexts'] as $key => $taskContext) {

                            // __ Пропускаем рулоны, которые нельзя редактировать,
                            // __ но самим физическим рулонам присваиваем порядковый номер
                            if ($taskContext['editable'] === false) {
                                $rolls = FabricTaskRoll::query()
                                    ->where('fabric_task_context_id', $taskContext['id'])
                                    ->get();

                                foreach ($rolls as $roll) {
                                    $roll->roll_position = $count++;
                                    $roll->save();
                                }

                                continue;
                            };

                            // __ Создаем  все физические рулоны, на основании плановых, или удаляем их
                            if ($mode) {

                                $fabric = Fabric::query()->find($taskContext['fabric_id']);
                                if (!$fabric) throw new Exception('Не найдено ПС');


                                for ($j = 0; $j < $taskContext['rolls_amount']; $j++) {
                                    $roll = FabricTaskRoll::query()->create(
                                        [
                                            'fabric_task_context_id' => $taskContext['id'],
                                            'fabric_id' => $taskContext['fabric_id'],
                                            'productivity' => $taskContext['productivity'],
                                            'description' => $taskContext['description'],   // дописываем плановый комментарий
                                            'roll_position' => $count++,
                                            'roll_status' => FABRIC_ROLL_CREATED_CODE,
                                            'translate_rate' => $fabric->translate_rate,
                                            'textile_roll_length' => $fabric->average_roll_length * $fabric->textile_layers_amount,
                                            'fabric_roll_length' => $fabric->translate_rate === 0.0 ? 0.0 : $fabric->average_roll_length / $fabric->translate_rate,
                                            // 'textile_roll_length' => $taskContext['average_textile_length'],
                                            // 'translate_rate' => $taskContext['translate_rate'],
                                        ]
                                    );

                                    // $count++;
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

//            return EndPointStaticRequestAnswer::ok();
//        } catch (Exception $e) {
//            return EndPointStaticRequestAnswer::fail(response()->json($e));
//        }
    }


//    /**
//     * Descr: Удаляем контекст СЗ по id
//     * @param Request $request
//     * @return string
//     */
//    public function deleteContext(Request $request)
//    {
//        try {
//
//            if (!$request->has('id')) return EndPointStaticRequestAnswer::fail();
//
//            $validData = $request->validate([
//                'id' => 'numeric|required',
//            ]);
//
//            $taskContext = FabricTaskContext::query()->find($validData['id']);
//
//            if ($taskContext) $taskContext->delete();
//
//            return EndPointStaticRequestAnswer::ok();
//        } catch (Exception $e) {
//            return EndPointStaticRequestAnswer::fail(response()->json($e));
//        }
//    }

    /**
     * Возвращаем результат создания или обновления дня заданий
     * @param $tasksDayData
     * @return string
     */
    private function createOrUpdateTasksDate($tasksDayData)
    {
        if (is_null($tasksDayData)) return null;

//        try {

        // Базовый набор данных, которые всегда заполняем
        $basicInsertData =
            [
                'tasks_date' => $tasksDayData['date'],
                'tasks_status' => $tasksDayData['common']['status'],
                'user_id' => Auth::id(),                                    // Текущий пользователь
                'description' => $tasksDayData['common']['description'] ?? null,

                // todo: сделать проверку на существование бригады и вообще тут продумать сущность
                'fabric_team_id' => FabricService::getFabricTeamChangeNumberByDate($tasksDayData['date']),
                'active' => $tasksDayData['active'] ?? true,
            ];

        // заполняем моменты начала и окончания группы заданий по стегальным машинам
        if ($tasksDayData['common']['status'] === FABRIC_TASK_RUNNING_CODE) {
            $basicInsertData['tasks_start_date'] = now();
            $basicInsertData['tasks_finish_date'] = null;
        } else if ($tasksDayData['common']['status'] === FABRIC_TASK_DONE_CODE) {
            $basicInsertData['tasks_finish_date'] = now();
        }

        // Возвращаем результат создания или обновления дня заданий
        return FabricTasksDate::query()->updateOrCreate(
            [
                'tasks_date' => $tasksDayData['date'],
            ],
            $basicInsertData
        );

//        } catch (Exception $e) {
//            return EndPointStaticRequestAnswer::fail(response()->json($e));
//        }

    }

    /**
     * Descr: Сохраняем или обновляем список сотрудников в группе заданий
     * @param Request $request
     * @return string
     */
    public function workersUpdate(Request $request)
    {
        // TODO: Жесткая валидация

        try {

            $taskId = $request->input('data')['data']['task'];

            if (!$taskId) throw new Exception('Не найден id группы заданий');

            // attract: получаем массив сетов: {"worker_id":1,"record_id":1}
            // attract: если record_id == 0, значит записи в таблице worker_record нет
            $workersSet = $request->input('data')['data']['workers'];

            if (!$workersSet) throw new Exception('Не найден массив сетов {worker_id-record_id}');

            // attract: получаем массив id в таблице worker_record
            $workerRecordsIds = [];
            foreach ($workersSet as $item) {
                if ($item['record_id'] !== 0) {
                    $workerRecordsIds[] = $item['record_id'];
                } else {

                    // attract: Создаем не существующие записи в таблице worker_record
                    $workerRecord = WorkerRecord::query()->firstOrCreate([
                        'worker_id' => $item['worker_id'],
                        'entity_id' => $taskId,                 // Код группы заданий: FabricTasksDate
                        'cell_entity_id' => FABRIC_ID           // Код условной ПЯ
                    ]);

                    $workerRecordsIds[] = $workerRecord->id;
                }
            }

            $task = FabricTasksDate::query()->find($taskId);    // Получаем группу заданий по id
            $task->workerRecord()->sync($workerRecordsIds);     // Синхронизируем массив id записей в таблице worker_record


            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * descr: Возвращает последнее выполненное СЗ
     * @return FabricTasksDateResource|string
     */
    public function getLastDoneTask()
    {
        try {

            $tasksQuery = FabricTasksDate::query()
                ->where('tasks_status', FABRIC_TASK_DONE_CODE)
                // relations добавляем основные + вложенные + user
                ->with([
                    'fabricTasks.fabricTaskContexts.fabricTaskRolls',
                    'fabricTasks.fabricTaskContexts.fabric',
                    'user'
                ])
                ->orderBy('tasks_date', 'desc')
                ->first();

            if (!$tasksQuery) {
                return json_encode(['data' => null]);
            }

            return new FabricTasksDateResource($tasksQuery);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    // TODO: Переписать код с использованием Scope getFabricExecutingTasks + getFabricNotDoneTasks и может getLastDoneTask

    // Descr: Возвращает список выполняемых СЗ
    // Descr: Если нет вообще параметра, то просто возвращаем список всех running СЗ
    // Descr: Если передан параметр date, но он null, то просто возвращаем список всех running СЗ до текущей даты
    // Descr: Если передан параметр date, то возвращаем список всех running СЗ до переданной даты
    /**
     * @param Request $request
     * @return FabricTasksDateCollection|false|string
     */
    public function getFabricExecutingTasks(Request $request)
    {
        try {

            // attract: Проверяем есть ли в запросе параметр date
            if (!$request->has('date')) {

                $tasksQuery = FabricTasksDate::query()
                    ->where('tasks_status', FABRIC_TASK_RUNNING_CODE)
                    ->orderBy('tasks_date')
                    ->get();

            } else {

                // attract: Если есть, то проверяем, не null ли он
                $payloadDate = is_null($request->date) ? now() : $request->validate(['date' => 'date_format:Y-m-d'])['date'];
                $tasksQuery = FabricTasksDate::query()
                    ->where('tasks_status', FABRIC_TASK_RUNNING_CODE)
                    ->where('tasks_date', '<', $payloadDate)
                    ->orderBy('tasks_date')
                    ->get();

            }

            if (!$tasksQuery) {
                return json_encode(['data' => null]);
            }

            return new FabricTasksDateCollection($tasksQuery);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    // Descr: Возвращает список СЗ, у которых статус до определенной даты не "Выполнен"
    // Descr: Если нет вообще параметра, то просто возвращаем список всех СЗ
    // Descr: Если передан параметр date, но он null, то просто возвращаем список всех СЗ до текущей даты
    // Descr: Если передан параметр date, то возвращаем список всех СЗ до переданной даты
    /**
     * @param Request $request
     * @return FabricTasksDateCollection|false|string
     */
    public function getFabricNotDoneTasks(Request $request)
    {
        try {

            // attract: Проверяем есть ли в запросе параметр date
            if (!$request->has('date')) {

                $tasksQuery = FabricTasksDate::query()
                    ->whereNot('tasks_status', FABRIC_TASK_DONE_CODE)
                    ->orderBy('tasks_date')
                    ->get();

            } else {

                // attract: Если есть, то проверяем, не null ли он
                $payloadDate = is_null($request->date) ? now() : $request->validate(['date' => 'date_format:Y-m-d'])['date'];
                $tasksQuery = FabricTasksDate::query()
                    ->whereNot('tasks_status', FABRIC_TASK_DONE_CODE)
                    ->where('tasks_date', '<', $payloadDate)
                    ->orderBy('tasks_date')
                    ->get();

            }

            if (!$tasksQuery) {
                return json_encode(['data' => null]);
            }

            return new FabricTasksDateCollection($tasksQuery);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }

    // ___ Закрываем СЗ
    // ___ Если нет вообще параметра, то закрываем текущую дату
    // ___ Если передан параметр date, то закрываем СЗ по указанной дате
    public function closeFabricTasks(Request $request)
    {
//        try {

            $payloadDate = is_null($request->date) ? now() : $request->validate(['date' => 'date_format:Y-m-d'])['date'];

            // attract: Проверяем, есть ли запущенные
            $runningTasks = $this->getFabricExecutingTasks(new Request(['date' => $payloadDate]));
            if (count($runningTasks) !== 0) {
//            throw new \Exception('Сменное задание не существует');
                return EndPointStaticRequestAnswer::fail('Предыдущие СЗ находятся в процессе выполнения');
            }

            // attract: Проверяем, есть ли еще не завершенные СЗ
            $notDoneTasks = $this->getFabricNotDoneTasks(new Request(['date' => $payloadDate]));
            if (count($notDoneTasks) !== 0) {
//            throw new \Exception('Сменное задание не существует');
                return EndPointStaticRequestAnswer::fail('Есть задания с непонятным до данной даты статусом');
            }

            // __ Получаем СЗ по указанной дате
            $targetTask = $this->tasks(new Request(['start' => $payloadDate, 'end' => $payloadDate]));

            // __ Проверяем существование СЗ
            if (!$targetTask) {
//            throw new \Exception('Сменное задание не существует');
                return EndPointStaticRequestAnswer::fail('Сменное задание не существует');
            }

            // __ Получаем первый элемент массива, т.к. возвращается коллекция и преобразуем в массив
            $targetTask = $targetTask[0]->resolve();

            // hr---------------------------------------------------------------------
            // __ Закрываем СЗ. Вся логика закрытия СЗ находится здесь

            // __ Получаем массив всех рулонов, которые нужно переместить на следующее СЗ и которые нужно добавить в буфер
            $rollsToMove = [];      // собираем все рулоны, которые нужно переместить
            $rollsToBuffer = [];    // собираем все рулоны, которые нужно добавить в буфер

            for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {

                // Получаем название машины
                $machineStr = FabricService::getFabricMachineNameById($i);

                // Проверка на наличие данных для данной стегальной машины
                if (count($targetTask['machines'][$machineStr]['rolls']) === 0) continue;

                foreach ($targetTask['machines'][$machineStr]['rolls'] as $roll) {
                    // $roll --> массив

                    $resolvedRoll = $roll->resolve();

                    foreach ($resolvedRoll['rolls_exec'] as $roll_exec) {
                        // $roll_exec --> Resource

                        // attract: Получаем данные из объекта Resource
                        $roll_exec_array = $roll_exec->resolve();

                        // attract: Если рулон помечен как не выполненный или помечен как переходящий
                        if ($roll_exec_array['status'] === FABRIC_ROLL_FALSE_CODE ||
                            $roll_exec_array['status'] === FABRIC_ROLL_ROLLING_CODE) {

                            $rollsToMove[] = [
                                'roll' => $resolvedRoll,
                                'roll_exec' => $roll_exec_array,
                                'machine_id' => $i,
                            ];

                        } else if ($roll_exec_array['status'] === FABRIC_ROLL_DONE_CODE) {

                            // __ Перенесли логику: буфер после выполнения рулона
                            $rollsToBuffer[] = [
                                'roll' => $resolvedRoll,
                                'roll_exec' => $roll_exec_array,
                                'machine_id' => $i,
                            ];
                        }
                    }
                }
            }


            // attract: Если есть рулоны к перемещению - перемещаем
            if (count($rollsToMove) !== 0) {

                // attract: Получаем дату следующего СЗ
                $nextDate = getCorrectDate($payloadDate)->addDay()->format('Y-m-d');


                // __ Тут реализуем следующую логику:
                // __ 1. Статус СЗ следующего дня не меняем!!! То есть, если СЗ нет - создаем, иначе статус не меняем
                // __ 2. Получаем дату следующего СЗ
                // __ 3. Получаем СЗ по дате
                // __ 4. Если СЗ не существует - создаем

                // __ Задаем дату следующего СЗ
                $tasksDayData['date'] = $nextDate;

                // __ Получаем следующее СЗ
                $nextTasksDate = FabricTasksDate::query()
                    ->where('tasks_date', $nextDate)
                    ->first();

                // __ Задаем статус следующего СЗ. Если СЗ не существует - создаем
                $tasksDayData['common']['status'] = is_null($nextTasksDate) ? FABRIC_TASK_CREATED_CODE : $nextTasksDate->tasks_status;


                // Warning: __ Старая логика
                // __ Задаем статус следующего СЗ
                // __ При таком подходе нужно дописать код, который в случае перехода от
                // __ "Готов к стежке" к "Создано" нужно будет удалять все физические рулоны
//                $tasksDayData['common']['status'] = FABRIC_TASK_CREATED_CODE;
                // Warning: ------------------------------------------

                // attract: Создаем новое СЗ или обновляем статус существующего и одновременно получаем его
                $nextTasksDate = $this->createOrUpdateTasksDate($tasksDayData);

                // TODO: Добавить изменение статусов в FabricTask таблице в соответствии с FabricTasksDate статусом

                $falseRollsPositionIndex = EXEC_ROLL_FALSE_ORDER_INDEX;
                foreach ($rollsToMove as $rollToMove) {

                    // attract: Создаем или обновляем саму сущность СЗ, к которому привязываем задание от ОПП и рулоны
                    $task = FabricTask::query()->updateOrCreate(
                        [
                            'fabric_tasks_date_id' => $nextTasksDate->id,
                            'fabric_machine_id' => $rollToMove['machine_id'],
                        ],
                        [
                            'fabric_tasks_date_id' => $nextTasksDate->id,
                            'fabric_machine_id' => $rollToMove['machine_id'],
                            'task_status' => $nextTasksDate->tasks_status,             // записываем пока общий статус всего СЗ
                            'fabric_team_id' => $nextTasksDate->fabric_team_id,
//                        'active' => $taskData['active'],
//                        'task_finish_at' => $taskData['finish_at'],
//                        'description' => $taskData['description'],
                        ]
                    );

//                    return ['rollToMove' => $rollToMove];

                    // attract: Создаем контекст задания
                    $taskContext = FabricTaskContext::query()->create(
                        [
                            'fabric_task_id' => $task->id,
                            'fabric_id' => $rollToMove['roll']['fabric_id'],
                            'roll_position' => $rollToMove['roll_exec']['status'] === FABRIC_ROLL_FALSE_CODE ?
                                $falseRollsPositionIndex++ : EXEC_ROLL_ROLLING_ORDER_INDEX,
                            'fabric_mode' => $rollToMove['roll']['fabric_mode'],
                            'rolls_amount' => 1,
                            'average_textile_length' => $rollToMove['roll_exec']['textile_length'],
                            'translate_rate' => $rollToMove['roll']['rate'],
                            'productivity' => $rollToMove['roll']['productivity'],
                            'description' => $rollToMove['roll']['descr'],
                            'note' => 'Из СЗ от ' . (Carbon::parse($payloadDate))->format('d.m.Y') . ': ' . $rollToMove['roll_exec']['false_reason'],
                            'editable' => false, // Делаем задание не редактируемым - признак того, что рулон переходящий
                        ]
                    );


                    // attract: Создаем рулон под этот контекст задания
                    // attract: Получаем этот рулон из базы данных, для меньшего заполнения полей
                    $fabricTaskContextIdOld = $rollToMove['roll_exec']['id'];

                    $roll = FabricTaskRoll::query()->find($fabricTaskContextIdOld);

                    $roll->task_context_id_old = $rollToMove['roll']['id'];     // Запоминаем старый id
                    $roll->fabric_task_context_id = $taskContext->id;           // Привязываем к новому контексту
                    $roll->movable = false;                                     // Запрещаем перемещение рулона на следующее СЗ

                    $history = json_decode($roll->history, true);
                    $history['previous_responsible_id'] = $rollToMove['roll_exec']['finish_by'];
                    $roll->history = json_encode($history);
                    $roll->finish_by = 0;

                    $roll->save();


                }

            }

            // __ Получаем дату следующего СЗ
            $nextDate = getCorrectDate($payloadDate)->addDay()->format('Y-m-d');

            // __ Получаем следующее СЗ
            $nextTasksDate = FabricTasksDate::query()
                ->where('tasks_date', $nextDate)
                ->first();

            // __ Проверяем, существует ли СЗ, если существует - переиндексируем
            if ($nextTasksDate) {

                // __ Обновляем порядковые номера выполняемых СЗ
                for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {

                    // __ Получаем само СЗ
                    $task = FabricTask::query()
                        ->where('fabric_tasks_date_id', $nextTasksDate->id)
                        ->where('fabric_machine_id', $i)
                        ->with('fabricTaskContexts')
                        ->first();

                    if (!$task) continue; // Если СЗ не существует - пропускаем

                    $task = $task->toArray();

                    // __ Сортируем по порядку
                    uasort($task['fabric_task_contexts'], function ($a, $b) {
                        return $a['roll_position'] <=> $b['roll_position'];
                    });

                    // __ Задаем начальные индексы
                    $contextOrderCount = EXEC_ROLL_START_ORDER_INDEX;
                    $rollOrderCount = EXEC_ROLL_START_ORDER_INDEX;

                    foreach ($task['fabric_task_contexts'] as $context) {

                        $contextModel = FabricTaskContext::query()
                            ->with('fabricTaskRolls')
                            ->find($context['id']);


//                        return ['$contextModel' => $contextModel];

                        foreach ($contextModel->fabricTaskRolls as $execRoll) {
                            $execRoll->update(['roll_position' => $rollOrderCount++]);
                        }

                        $contextModel->update(['roll_position' => $contextOrderCount++]);
                    }

                }

            }


            // attract: Если есть рулоны к добавлению в буфер - добавляем в буфер
            // warning: Логика закрытия рулона и добавления в буфер находится в контролере CellFabricTaskRollController
            // warning: Буфер увеличивается при изменении статуса рулона на 'Выполнено'

            /*
            if (count($rollsToBuffer) !== 0) {


                foreach ($rollsToBuffer as $rollToBuffer) {


                    $fabric = Fabric::query()->find($rollToBuffer['roll_exec']['fabric_id']); // Находим нужную ткань

                    if ($fabric) {

                        if ($fabric->translate_rate === 0) {
                            throw new Exception('У ' . $fabric->display_name . ' отсутствует коэффициент перевода!');
                        }

                        $fabric->buffer_amount += $rollToBuffer['roll_exec']['textile_length'] / $fabric->translate_rate;
                        $fabric->save();

                    } else {
                        throw new Exception('Не найдена ткань по ID: ' . $rollToBuffer['roll_exec']['fabric_id']);
                    }


                }


            }
            */


            // __ Задаем параметры для Дня СЗ, который нужно закрыть и закрываем
            $tasksDayData['date'] = $payloadDate;
            $tasksDayData['common']['status'] = FABRIC_TASK_DONE_CODE;
            $tasksDayData['common']['description'] = $targetTask['common']['description'];
            $resultTasksDate = $this->createOrUpdateTasksDate($tasksDayData);

//            return [
//                '$targetTask' => $targetTask,
//                'rollsToMove' => $rollsToMove,
//                'rollsToBuffer' => $rollsToBuffer,
//                'resultTasksDate' => $resultTasksDate
//
//            ];

            return EndPointStaticRequestAnswer::ok();
            // hr---------------------------------------------------------------------

//        } catch (Exception $e) {
//            return EndPointStaticRequestAnswer::fail(response()->json($e));
//        }
    }

}

