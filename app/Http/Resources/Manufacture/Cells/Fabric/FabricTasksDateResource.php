<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use App\Services\Manufacture\FabricService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
                    $rolls[] = [
                        'id' => $roll->id,
                        'fabric_id' => $roll->fabric_id,
                        'position' => $roll->roll_position,
                        'status' => $roll->roll_status,
                        'finish_at' => $roll->finish_at,
                        'finish_by' => $roll->finish_by,
                        'textile_length' => (float)$roll->textile_roll_length,
                        'productivity' => (float)$roll->productivity,
                        'descr' => $roll->description,
                    ];
                }


                $rollsContext[] = [
                    'id' => $rollContext->id,
                    'average_textile_length' => (float)$rollContext->average_textile_length,
                    'productivity' => (float)$rollContext->productivity,
                    'rolls_amount' => $rollContext->rolls_amount,
                    'length_amount' => $rollContext->rolls_amount * $rollContext->average_textile_length,
                    'fabric_id' => $rollContext->fabric_id,
                    'fabric' => '', // Здесь будет название ткани, получим на бэке название ткани
                    'fabric_mode' => $rollContext->fabric_mode,
                    'descr' => $rollContext->description,
                    'correct' => true,
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

        return [
            'date' => $this->tasks_date,
            'common' => [
                'active' => $this->active,
                'team' => 1,
                'status' => $this->tasks_status,
                'finish_at' => $this->finish_tasks_date,
                'description' => $this->description,
            ],
            'machines' => $machines,
//            'test' => $this->fabricTasks,
        ];

        //        return parent::toArray($request);
    }
}

