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
        // Заполняем массив с именами стегальных машин
        $machinesNames = [];
        for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {
            $machinesNames[] = FabricService::getFabricMachineNameById($i); // Получаем название машины
        }

        // Формируем массив с данными о машине
        $machines = [];
        foreach ($this->fabricTasks as $task) {
            $machineName = FabricService::getFabricMachineNameById($task->fabric_machine_id);
            $machines[$machineName] = [
                'active' => $task->active,
                'description' => $task->description,
                'finish_date' => $task->tasks_finish_date,
                'rolls' => [],          // Здесь будут все рулончики для данной машины
            ];

            // убираем те данные из массива, которые обработали
            $machinesNames = array_diff($machinesNames, [$machineName]);
        }

        // Добавляем те машины, которые нужно добавить в массив $machines, но нет в базе данных
        foreach ($machinesNames as $machineName) {
            $machines[$machineName] = [
                'active' => true,
                'description' => null,
                'finish_date' => null,
                'rolls' => [],
            ];
        }

        return [
            'date' => $this->tasks_date,
            'common' => [
                'active' => $this->active,
                'team' => 1,
                'status' => $this->tasks_status,
                'finish' => $this->finish_tasks_date,
                'description' => $this->description,
            ],
            'machines' => $machines
        ];

        //        return parent::toArray($request);
    }
}

