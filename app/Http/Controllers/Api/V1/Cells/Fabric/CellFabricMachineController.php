<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricMachineCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricMachineResource;
use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use Exception;
use Illuminate\Http\Request;


class CellFabricMachineController extends Controller
{
    /**
     * Attract: Возвращаем список стегальных машин
     * @return FabricMachineCollection
     */
    public function machines()
    {
        return new FabricMachineCollection(FabricMachine::all());
    }

    /**
     * Attract: Возвращаем стегальную машину по id
     * @param $id
     * @return FabricMachineResource
     */
    public function machine($id)
    {
        // todo Сделать проверку на существование машины
        return new FabricMachineResource(FabricMachine::query()->find($id));
    }


    public function setMachineActive(Request $request)
    {
        try {
            $validData = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|numeric',
                'data.active' => 'required|boolean'
            ]);

            $machine = FabricMachine::query()->findOrFail($request->input('data.id'));

            if (!$machine) {
                throw new Exception('Машина не найдена!');
            }

            // attract: Если меняем статус на неактивный, то удаляем все Task,
            // attract: кроме статуса "Выполнено" и "Выполняется", связанные с этой СМ
            if (!$request->input('data.active')) {

                // attract: Получаем все задачи, связанные с этой СМ, кроме статуса "Выполнено" и "Выполняется"
                $tasks = FabricTask::query()
                    ->where('fabric_machine_id', $machine->id)
                    ->whereHas('fabricTasksDate', function ($query) {
                        $query->whereNotIn('tasks_status', [FABRIC_TASK_RUNNING_CODE, FABRIC_TASK_DONE_CODE]);
                    })
                    ->get();

                // attract: Удаляем все найденные задачи
                foreach ($tasks as $task) {
                    $task->delete();
                }

                // attract: После предыдущего удаления, может возникнуть ситуация, когда в статус
                // attract: группы сменных заданий будет "Готово к стежке", а там не будет СЗ ни для одной СМ
                // attract: Находим такие задания и меняем статус на "Создано"
                $tasksDates = FabricTasksDate::query()
                    ->where('tasks_status', FABRIC_TASK_PENDING_CODE)
                    ->with('fabricTasks')
                    ->get();

                foreach ($tasksDates as $tasksDate) {
                    if (count($tasksDate->fabricTasks) === 0) {
                        $tasksDate->tasks_status = FABRIC_TASK_CREATED_CODE;
                        $tasksDate->save();
                    }
                }

            }

            // attract: меняем статус
            $machine->active = $request->input('data.active');
            $machine->save();

            return EndPointStaticRequestAnswer::ok();
//            return ['test' => $machine];
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }


    }


}
