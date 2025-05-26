<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskContextCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskContextResource;
use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use App\Services\Manufacture\FabricService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

}
