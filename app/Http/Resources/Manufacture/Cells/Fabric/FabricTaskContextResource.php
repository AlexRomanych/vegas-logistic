<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricTaskContextResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'average_textile_length' => $this->average_textile_length,
            'average_fabric_length' => $this->average_fabric_length,
            'comment' => $this->comment,
            'description' => $this->description,
            'fabric_id' => $this->fabric_id,
            'id' => $this->id,
            'note' => $this->note,
            'productivity' => (float)$this->productivity,
            'rolls_amount' => $this->rolls_amount,
            'translate_rate' => (float)$this->translate_rate,
            'roll_position' => $this->roll_position,

//            'active' => $this->active,
//            'editable' => $this->editable,
//            'fabric_mode' => $this->fabric_mode,
//            'fabric_task' => $this->fabric_task,
//            'fabric_task_id' => $this->fabric_task_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,

            'task' => [
                'id' => $this->fabricTask->id,
                'fabric_machine_id' => $this->fabricTask->fabric_machine_id,
                'task_status' => $this->fabricTask->fabricTasksDate->tasks_status,
                'task_date' => $this->fabricTask->fabricTasksDate->tasks_date,
                // 'task_status' => $this->fabricTask->task_status,
                // 'updated_at' => $this->fabricTask->updated_at,
                // 'user_id' => $this->fabricTask->user_id,
                // 'fabric_team_id' => $this->fabricTask->fabric_team_id,
                // 'fabric_tasks_date_id' => $this->fabricTask->fabric_tasks_date_id,
                // 'note' => $this->fabricTask->note,
                // 'task_finish_at' => $this->fabricTask->task_finish_at,
                // 'active' => $this->fabricTask->active,
                // 'comment' => $this->fabricTask->comment,
                // 'created_at' => $this->fabricTask->created_at,
                // 'description' => $this->fabricTask->description,

            ],

            // 'task_full' => $this->fabricTask,

        ];
//        return parent::toArray($request);
    }
}

