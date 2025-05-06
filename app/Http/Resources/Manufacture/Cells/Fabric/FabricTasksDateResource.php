<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use App\Http\Resources\Worker\WorkerCollection;
use App\Http\Resources\Worker\WorkerResource;
use App\Services\Manufacture\FabricService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FabricTasksDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        $rollsContext = [];
//        foreach ($this->fabricTasks->fabricTaskContexts as $rollContext) {
//            $rollsContext[] = $rollContext;
//        }

        // Заполняем массив с именами стегальных машин
        $machinesNames = [];
        for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {
            $machinesNames[] = FabricService::getFabricMachineNameById($i); // Получаем название машины
        }

        // Формируем массив с данными о машине
        $machines = [];
        foreach ($this->fabricTasks as $task) {

            $rollsContext = [];
            foreach ($task->fabricTaskContexts as $rollContext) {

                $rolls = [];
                foreach ($rollContext->fabricTaskRolls as $roll) {

                    $rolls[] = new FabricTaskRollResource($roll);


//                    $rolls[] = [
//                        'id' => $roll->id,
//                        'fabric_id' => $roll->fabric_id,
//                        'position' => $roll->roll_position,
//                        'status' => $roll->roll_status,
//                        'start_at' => $roll->start_at,
//                        'paused_at' => $roll->paused_at,
//                        'resume_at' => $roll->resume_at,
//                        'finish_at' => $roll->finish_at,
//                        'duration' => $roll->duration,
//                        'finish_by' => $roll->finish_by,
//                        'rolling' => $roll->rolling,
//                        'textile_length' => (float)$roll->textile_roll_length,
//                        'productivity' => (float)$roll->productivity,

//                        'descr' => $roll->description,
//                    ];
                }


                $rollsContext[] = [
                    'id' => $rollContext->id,
                    'rate' => (float)$rollContext->translate_rate,
                    'average_textile_length' => (float)$rollContext->average_textile_length,
                    'average_fabric_length'
                        => (float)$rollContext->average_textile_length/(float)$rollContext->translate_rate,
                    'productivity' => (float)$rollContext->productivity,
                    'buffer' => (float)$rollContext->fabric->buffer_amount,
                    'buffer_min' => (float)$rollContext->fabric->buffer_min,
                    'buffer_max' => (float)$rollContext->fabric->buffer_max,
                    'buffer_min_rolls' => (float)$rollContext->fabric->buffer_min_rolls,
                    'buffer_max_rolls' => (float)$rollContext->fabric->buffer_max_rolls,
                    'rolls_amount' => $rollContext->rolls_amount,
                    'length_amount' => $rollContext->rolls_amount * $rollContext->average_textile_length,
                    'fabric_id' => $rollContext->fabric_id,
                    'fabric' => $rollContext->fabric->display_name,
                    'fabric_rate' => (float)$rollContext->fabric->translate_rate,
                    'fabric_mode' => $rollContext->fabric_mode,
                    'descr' => $rollContext->description,
                    'correct' => true,
                    'editable' => $rollContext->editable,
                    'rolls_exec' => $rolls,
                ];
            }


            $machineName = FabricService::getFabricMachineNameById($task->fabric_machine_id);
            $machines[$machineName] = [
                'active' => $task->active,
                'description' => $task->description,
                'finish_at' => $task->tasks_finish_at,
                'rolls' => $rollsContext,          // Здесь будут все рулончики от ОПП для данной машины
            ];

            // убираем те данные из массива, которые обработали
            $machinesNames = array_diff($machinesNames, [$machineName]);
        }

        // Добавляем те машины, которые нужно добавить в массив $machines, но нет в базе данных
        foreach ($machinesNames as $machineName) {
            $machines[$machineName] = [
                'active' => true,
                'description' => null,
                'finish_at' => null,
                'rolls' => [],
            ];
        }

        // Выводим данные о работниках
        $workersData = ($this->workerRecord);
        $workers = [];

        foreach ($workersData as $key => $workerData) {
            // warning: Отдаем на фронт данных о записи в таблице worker_records, чтобы получить ее обратно
            // warning: и по ней синхронизировать данные в промежуточной таблице
            $workerData->worker->record_id = $workerData->id;
            $workers[] = new WorkerResource($workerData->worker);
        }

        return [
            'date' => $this->tasks_date,
            'id' => $this->id,
            'common' => [
                'active' => $this->active,
                'team' => $this->team->team_no,
                'status' => $this->tasks_status,
                'start_at' => $this->tasks_start_date ? (new Carbon($this->tasks_start_date))->format('Y-m-d H:i:s') : null,
                'finish_at' => $this->tasks_finish_date ? (new Carbon($this->tasks_finish_date))->format('Y-m-d H:i:s') : null,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),       // Laravel автоматически преобразует строку в объект Carbon
                'created_by' => $this->user->name,
                'description' => $this->description,
            ],
            'workers' => $workers,
            'machines' => $machines,
//            'test' => $this->fabricTasks,
        ];

        //        return parent::toArray($request);
    }
}

