<?php /** @noinspection ALL */

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskContextCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskContextResource;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskContextResourceDate;
use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use App\Services\LogicalService;
use App\Services\Manufacture\FabricService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

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

            $taskContext = FabricTaskContext::query()
                ->with('fabricTask')
                ->find($validData['id']);

            $taskId = $taskContext->fabricTask->id;

            if ($taskContext) {
                $taskContext->delete();
                FabricService::reindexOrderContexts($taskId);
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Получаем контекст СЗ (то, что выставлено ООП) со всеми статусами, кроме 'выполнено'
     * @return AnonymousResourceCollection|string
     */
    public function getContextNotDone()
    {
        try {

            $taskContexts = FabricTaskContext::query()
                ->whereHas('fabricTask', function ($query) {
                    $query->whereHas('fabricTasksDate', function ($query) {
                        $query->whereNot('tasks_status', FABRIC_TASK_DONE_CODE);
                    });
                })
                ->with('fabricTask.fabricTasksDate')
                ->get();


            // $taskContexts = FabricTaskContext::query()
            //     ->whereHas('fabricTask', function ($query) {
            //         $query->whereNot('task_status', FABRIC_TASK_DONE_CODE);
            //     })
            //     ->with('fabricTask.fabricTasksDate')
            //     ->get();

            return FabricTaskContextResource::collection($taskContexts);
            // return new FabricTaskContextCollection($taskContexts);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * Descr: Создаем контекст СЗ по заданным параметрам из расхода ПС
     * @param Request $request
     * @return FabricTaskContextResource|string
     */
    public function createContextExpense(Request $request)
    {
        try {

            $result = $request->validate([
                'data.data' => 'required|array',
                'data.data.date' => 'nullable|date_format:Y-m-d',
                'data.data.fabric_id' => 'required|numeric',
                'data.data.fabric_expense' => 'required|numeric'
            ]);

            $contextExpenseData = $request->input('data')['data'];

            $contextDate = $contextExpenseData['date'] ?? (Carbon::now())->format('Y-m-d');

            // attract: Получаем дату следующего СЗ
            $nextDate = getCorrectDate($contextDate)->addDay()->format('Y-m-d');

            // attract: Получаем СЗ по указанной дате
            $targetTask = FabricTasksDate::query()
                ->where('tasks_date', $nextDate)
                ->first();

            // attract: Проверяем существование СЗ
            if ($targetTask) {

                $targetTask->tasks_status = FABRIC_TASK_CREATED_CODE;
                $targetTask->save();

                // attract: Обновляем статусы СЗ на 'Создано'
                for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {

                    //                // Получаем название машины
                    //                $machineStr = FabricService::getFabricMachineNameById($i);

                    // attract: Получаем само СЗ
                    $task = FabricTask::query()
                        ->where('fabric_tasks_date_id', $targetTask->id)
                        ->where('fabric_machine_id', $i)
                        ->first();

                    if ($task) {
                        $task->task_status = FABRIC_TASK_CREATED_CODE;
                        $task->save();
                    }

                }

            } else {

                // attract: Создаем само СЗ, коль его нет
                $targetTask = FabricTasksDate::query()->create(
                    [
                        'tasks_date' => $nextDate,
                        'tasks_status' => FABRIC_TASK_CREATED_CODE,
                        'user_id' => Auth::id(),  // Текущий пользователь
                        'fabric_team_id' => FabricService::getFabricTeamChangeNumberByDate($nextDate),
                        'active' => true,
                    ]);

            }

            // attract: Получаем ПС, для получения основной СМ
            $fabric = Fabric::query()->find($contextExpenseData['fabric_id']);

            // attract: Получаем Task, для создания TaskContext
            $task = FabricTask::query()
                ->where('fabric_tasks_date_id', $targetTask->id)
                ->where('fabric_machine_id', $fabric->fabricPicture->fabric_machine_id)
                ->with('fabricTaskContexts')
                ->first();

            if ($task) {

                // attract: Если есть - удаляем физические рулоны, если они есть, потому что поменяли статус СЗ
                foreach ($task->fabricTaskContexts as $context) {
                    FabricTaskRoll::query()
                        ->where('fabric_task_context_id', $context->id)
                        ->delete();
                }

            } else {

                // attract: Создаем Task, коль такого нет
                $task = FabricTask::query()->create(
                    [
                        'fabric_tasks_date_id' => $targetTask->id,
                        'fabric_machine_id' => $fabric->fabricPicture->fabric_machine_id,
                        'fabric_team_id' => $targetTask->fabric_team_id,
                        'user_id' => $targetTask->user_id,
                        'task_status' => FABRIC_TASK_CREATED_CODE,
                        'active' => true,
                    ]
                );
            }

            // attract: Определяем кол-во в рулонах: если дельта равна нулю, то один рулон, нет - округление вверх
            $expense = abs((float)$contextExpenseData['fabric_expense']);
            $expense = $fabric->translate_rate === 0.0 ? $expense : $expense * $fabric->translate_rate;   // Переводим ПС в ткань
            $rollsAmount = $expense === 0.0 ? 1 : ceil($expense / $fabric->average_roll_length);

            // attract: Создаем контекст для задания
            $taskContext = FabricTaskContext::query()->create([
                'fabric_task_id' => $task->id,
                'fabric_id' => $fabric->id,
                'roll_position' => 1,
                'average_textile_length' => $fabric->average_roll_length,
                'productivity' => $fabric->productivity,
                'translate_rate' => $fabric->translate_rate,
                'rolls_amount' => $rollsAmount,
                'note' => 'Из расхода ПС от ' . (Carbon::parse($contextDate))->format('d.m.Y'),
            ]);

            return new FabricTaskContextResource($taskContext);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * ___ Меняет порядок рулонов контекста СЗ
     * @return string
     * @throws Throwable
     */
    public function changeContextOrder(Request $request)
    {

        try {

            $payload = $request->validate([
                'data' => 'required|array',
                'data.task' => 'required|integer',
                'data.machine' => 'required|integer',
                'data.context' => 'required|array',
            ]);

            $data = $payload['data']; // одна data в axios, вторая data в самом объекте task


            $task = FabricTask::query()
                ->where('fabric_tasks_date_id', $data['task'])
                ->with('fabricTaskContexts')
                ->first();

            if (!$task) {
                throw new Exception('Task not found');
            }

            // __ Проверяем на правильность следования рулонов с фронта
            $contextData = FabricService::checkContextOrder($task, $data['context']);

            DB::transaction(function () use ($contextData, $task) {

                foreach ($contextData as $context) {

                    // __ плановый рулон от ОПП
                    $workContext = FabricTaskContext::query()->find($context['id']);

                    // $workContext = FabricTaskContext::query()
                    //     ->where('fabric_task_id', $task->id)
                    //     ->where('fabric_id', $context['fabric_id'])
                    //     ->where('editable', true)
                    //     ->first();

                    if ($workContext) {             // если есть (рулон может быть удален), то меняем порядок
                        $workContext->roll_position = $context['roll_position'];
                        $workContext->save();
                    }
                }


            });

            return EndPointStaticRequestAnswer::ok();

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * __ Получаем контекст СЗ по СЗ и стегальной машине
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getOrderContext(Request $request)
    {
        try {
            $payload = $request->validate([
                'task' => 'required|integer',           // task_id
                'machine' => 'required|integer',        // machine_id
            ]);

            $task = FabricTask::query()
                ->where('fabric_tasks_date_id', $payload['task'])
                ->where('fabric_machine_id', $payload['machine'])
                ->with(['fabricTaskContexts.fabricTaskRolls', 'fabricTaskContexts.fabric'])
                ->first();

            return FabricTaskContextResourceDate::collection($task->fabricTaskContexts);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * ___ Добавляем рулон в контекст СЗ
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function addOrderContextRoll(Request $request)
    {
        try {
            $payload = $request->validate([
                // 'data' => 'required|array',
                'task' => 'required|integer',
                'machine' => 'required|integer',
                'roll' => 'required|array',

                'roll.id' => 'required|integer',
                'roll.fabric_id' => 'required|integer',
                'roll.average_textile_length' => 'required|numeric',
                'roll.productivity' => 'required|numeric',
                'roll.textile_length' => 'required|numeric',
                'roll.rolls_amount' => 'required|integer|min:1',
                'roll.fabric_mode' => 'required|boolean',
                'roll.rate' => 'required|numeric',
                'roll.editable' => 'required|boolean',
                'roll.roll_position' => 'required|integer',
                'roll.descr' => 'nullable|string',

            ]);

            // __ Получаем СЗ на СМ
            $task = FabricTask::query()
                ->where('fabric_tasks_date_id', $payload['task'])
                ->where('fabric_machine_id', $payload['machine'])
                ->first();

            // __ Проверяем наличие СЗ. Если нет (возможно это первый рулон), то создаем
            if (!$task) {
                $task = FabricTask::query()->create(
                    [
                        'fabric_tasks_date_id' => $payload['task'],
                        'fabric_machine_id' => $payload['machine'],
                        'task_status' => FABRIC_TASK_CREATED_CODE,
                    ]
                );
            }

            $rollData = $payload['roll'];

            $fabric = Fabric::query()->find($rollData['fabric_id']);
            if (!$fabric) throw new Exception('Fabric not found');

            // __ Задаем загрузку модели
            $payloadData = [
                'fabric_task_id' => $task->id,                          // привязка к СЗ
                'fabric_id' => $rollData['fabric_id'],                  // привязка к ПС
                'rolls_amount' => $rollData['rolls_amount'],
                'roll_position' => $rollData['roll_position'],          // порядок рулонов
                'fabric_mode' => $rollData['fabric_mode'],
                'average_textile_length' => $rollData['textile_length'],
                'productivity' => $rollData['productivity'],
                'description' => $rollData['descr'],
                'translate_rate' => $fabric->translate_rate,            // Берем из ПС
            ];

            // __ плановый рулон от ОПП

            if ($rollData['id'] === -1) {
                $taskRollContext = FabricTaskContext::query()->create(
                    $payloadData
                );
            } else {

                $taskRollContext = FabricTaskContext::query()->updateOrCreate(
                    [
                        'id' => $rollData['id'],
                    ],
                    $payloadData
                // [
                //     'fabric_task_id' => $task->id,                          // привязка к СЗ
                //     'fabric_id' => $rollData['fabric_id'],                  // привязка к ПС
                //     'rolls_amount' => $rollData['rolls_amount'],
                //     'roll_position' => $rollData['roll_position'],          // порядок рулонов
                //     'fabric_mode' => $rollData['fabric_mode'],
                //     'average_textile_length' => $rollData['textile_length'],
                //     'productivity' => $rollData['productivity'],
                //     'description' => $rollData['descr'],
                //     'translate_rate' => $fabric->translate_rate,            // Берем из ПС
                // ]
                );
            }

            return FabricTaskContextResourceDate::collection($task->fabricTaskContexts);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }



    /**
     * ___ Оптимизируем контекст СЗ по времени переналадки
     * @param int $task
     * @param int $machine
     * @param int $statistic    - 0 - без статистики, 1 - возврат статистики выполнения скрипта (время + память)
     * @return array
     * @throws Exception
     */
    public function optimizeOrderContext(int $task, int $machine, int $statistic = 0)
    {
        // try {
        $validator = validator([
            'task' => $task,
            'machine' => $machine,
            'statistic' => $statistic,
        ], [
            'task' => 'required|integer',           // task_id
            'machine' => 'required|integer',        // machine_id
            'statistic' => 'nullable|in:0,1',       // statistic 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        // __ Получаем СЗ на СМ
        $task = FabricTask::query()
            ->where('fabric_tasks_date_id', $task)
            ->where('fabric_machine_id', $machine)
            ->with([
                'fabricTaskContexts.fabric.fabricPicture',
                'fabricTaskContexts.fabric.fabricPicture.picturesFrom',
                // 'fabricTaskContexts.fabric.fabricPicture.picturesFrom.picTo',
                // 'picturesFrom.picTo.fabricMainMachine'
            ])
            ->first();

        if (!$task) {
            throw new Exception('Task not found');
        }

        // __ Получаем рисунок последнего рулона
        $taskDate = FabricTasksDate::query()->find($task->fabric_tasks_date_id);
        if (!$taskDate) {
            throw new Exception('Tasks not found');
        }
        $lastRoll = FabricService::getLastRoll($taskDate->tasks_date, $machine);

        /**
         * ___ Получаем ключ ассоциативного массива времени переналадки для доступа O(1)
         * @param int $index1
         * @param int $index2
         * @return string
         */
        function getListKey(int $index1, int $index2): string
        {
            return $index1 . '-' . $index2;
        }

        // __ Формируем массив с уникальными Рисунками
        $uniquePics = [];

        // __ Добавляем рисунок последнего рулона
        if ($lastRoll) {
            $fabric = $lastRoll->fabric;
            $picture = ($fabric->toArray())['fabric_picture'];
            $uniquePics[$picture['id']] = $picture; // Добавляем в массив
        }

        foreach ($task->fabricTaskContexts as $context) {
            $fabricPic = $context->fabric->fabricPicture;
            $uniquePics[$fabricPic->id] = $fabricPic;
        }

        // __ Формируем матрицу с временем переналадки и ассоциативный массив со временем
        $tuningTimes = [];
        $tuningTimesMatrix = [];
        $minTime = 0;   // Максимальное время переналадки (для сравнения)
        $errors = [];   // Массив ошибок
        $MISSING_TUNING_TIME = -1;

        foreach ($uniquePics as $iH => $picH) {
            foreach ($uniquePics as $iV => $picV) {

                $time = $MISSING_TUNING_TIME;
                if ($iH !== $iV) {

                    $picHArray = $picH->toArray();

                    foreach ($picHArray['pictures_from'] as $picItem) {
                        if ($picItem['picture_to'] === $iV) {
                            $time = $picItem['tuning_time'];
                            break;
                        }
                    }
                }

                // Матрицу не используем пока
                // $tuningTimesMatrix[$iH][$iV] = [
                //     'from' => $picH->id,
                //     'to' => $picV->id,
                //     'time' => $time,
                // ];

                if ($time === $MISSING_TUNING_TIME && $iH !== $iV) {
                    $errors[] = 'Нет времени переналадки с рис. ' . $picH->name . ' на рис. ' . $picV->name;
                    $time = 0;
                }

                $tuningTimes[getListKey($picH->id, $picV->id)] = $time;
                $minTime += $time;
            }
        }

        $outData['data'] = [
            'minTime' => 0,
            'permutation' => [],
            'errors' => $errors,
        ];

        if (count($errors) > 0) return $outData;    // Если есть ошибки, то выходим

        // __ Сами расчеты
        $startTime = microtime(true);

        // Тут можно сделать алгоритм на 1 итерацию меньше, если не включать $lastRoll в $uniquePics
        $picKeys = array_keys($uniquePics);
        // $picKeys = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; // Тестовый массив. 10 рисунков - 5 секунд. 11 - 55 сек.

        $startPicId = $lastRoll ? $picture['id'] : 0; // Первый рисунок (переходящий рисунок)
        // $startPicId = $lastRoll ? $picture['id'] : $picKeys[0]; // Первый рисунок (переходящий рисунок)

        // __ Оставляем закомментированными для уменьшения мспользования памяти
        $permutations = [];     // Все перестановки рисунков
        // $permutations = $lastRoll ? [$picture['id']] : [];     // Все перестановки рисунков
        $minTimes = [];         // Время для каждой перестановки
        // $permutations = LogicalService::getPermutations($picKeys);

        $permutationMin = $picKeys;

        foreach (LogicalService::getPermutationsGenerator($picKeys) as $permutation) {
            // Для оптимизации нужно убрать эту строку
            if ($startPicId !==0 && $permutation[0] !== $startPicId) continue;  // Пропускаем перестановку, если задан начальный рисунок

            $permutationsTime = 0;

            for ($i = 0; $i < count($permutation) - 1; $i++) {
                $permutationsTime += $tuningTimes[getListKey($permutation[$i], $permutation[$i + 1])];
            }

            // Тут могут быть и несколько перестановок с одинаковым временем
            if ($permutationsTime < $minTime) { // Первая найденная перестановка, $permutationsTime <= $minTime - последняя
                $minTime = $permutationsTime;
                $permutationMin = $permutation;
            }

            // __ Оставляем закомментированными для уменьшения использования памяти
            // $permutations[] = $permutation;
            // $minTimes[] = $permutationsTime;
        }

        // __ Сопоставляем перестановку с контекстными рулонами
        $contextRolls = $task->fabricTaskContexts->toArray();

        // __ Сортируем по позиции
        usort($contextRolls, function ($a, $b) {
            return $a['roll_position'] <=> $b['roll_position']; // Для сортировки по возрастанию
        });

        $resultPermutations = [];
        foreach ($permutationMin as $key => $picId) {
            $groupPermutations = [];

            foreach ($contextRolls as $contextRoll) {
                if ($contextRoll['fabric']['fabric_picture']['id'] === $picId) {
                    $groupPermutations[] = $contextRoll;
                }
            }

            // Сортируем еще и по имени ткани, чтобы ткани были упорядочены
            usort($groupPermutations, function ($a, $b) {
                return $a['fabric']['textile'] <=> $b['fabric']['textile']; // Для сортировки по возрастанию
            });

            $resultPermutations = array_merge($resultPermutations, $groupPermutations);
        }


        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $outData['data'] = [
            'minTime' => $minTime,
            'permutation' => $resultPermutations,
            'errors' => null,
        ];

        if ($statistic) {
            $outData['statistic'] = [
                'uniquePics' => $uniquePics,
                'keys' => $picKeys,
                'times' => $tuningTimes,
                'executionTime' => $executionTime . ', sec.',
                'memory' => memory_get_peak_usage(true) / 1024 / 1024 . ', MB',
                // 'matrix' => $tuningTimesMatrix,
                // 'minTimes' => $minTimes,            // Оставляем закомментированными для уменьшения использования памяти
                // 'permutations' => $permutations,    // Оставляем закомментированными для уменьшения использования памяти
            ];
        }

        $outData['debug'] = $permutationMin;

        return $outData;

        // } catch (Exception $e) {
        //     return $e;
        //     return EndPointStaticRequestAnswer::fail(response()->json($e));
        // }
    }

}
