<?php

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

            'average_textile_length' => (float)$this->average_textile_length,
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
                'task_status' => $this->fabricTask->task_status,

//                'updated_at' => $this->fabricTask->updated_at,
//                'user_id' => $this->fabricTask->user_id,
//                'fabric_team_id' => $this->fabricTask->fabric_team_id,
//                'fabric_tasks_date_id' => $this->fabricTask->fabric_tasks_date_id,
//                'fabric_machine_id' => $this->fabricTask->fabric_machine_id,
//                'description' => $this->fabricTask->description,
//                'created_at' => $this->fabricTask->created_at,
//                'comment' => $this->fabricTask->comment,
//                'active' => $this->fabricTask->active,
//                'task_finish_at' => $this->fabricTask->task_finish_at,
//                'note' => $this->fabricTask->note,

            ],
        ];
//        return parent::toArray($request);
    }
}

