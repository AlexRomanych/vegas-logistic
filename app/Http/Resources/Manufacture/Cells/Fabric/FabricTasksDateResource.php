<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use App\Http\Resources\Worker\WorkerResource;
use App\Services\Manufacture\FabricService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

// use App\Http\Resources\Worker\WorkerCollection;

class FabricTasksDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // __Заполняем массив с именами стегальных машин
        $machinesNames = [];
        for ($i = FABRIC_MACHINE_AMERICAN_ID; $i <= FABRIC_MACHINE_KOREAN_ID; $i++) {
            $machinesNames[] = FabricService::getFabricMachineNameById($i); // Получаем название машины
        }

        // __Формируем массив с данными о машине
        $machines = [];
        foreach ($this->fabricTasks as $task) {

            $machineName = FabricService::getFabricMachineNameById($task->fabric_machine_id);
            $machines[$machineName] = [
                'active' => $task->active,
                'description' => $task->description,
                'finish_at' => $task->tasks_finish_at,
                'rolls' => FabricTaskContextResourceDate::collection($task->fabricTaskContexts),          // Здесь будут все рулончики от ОПП для данной машины
                'lastExecRoll' => FabricTaskRollResource::make(
                    FabricService::getLastRoll($this->tasks_date, $task->fabric_machine_id)
                )
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
                'lastExecRoll' => FabricTaskRollResource::make(
                    FabricService::getLastRoll(
                        $this->tasks_date,
                        FabricService::getFabricMachineIdByName($machineName)
                    )
                )
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
                'id' => $this->id,
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

