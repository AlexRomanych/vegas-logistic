<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricTaskRollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fabric_id' => $this->fabric_id,
            'position' => $this->roll_position,
            'status' => $this->roll_status,
            'start_at' => $this->start_at,
            'paused_at' => $this->paused_at,
            'resume_at' => $this->resume_at,
            'finish_at' => $this->finish_at,
            'duration' => $this->duration,
            'finish_by' => $this->finish_by,
            'rolling' => $this->rolling,
            'textile_length' => (float)$this->textile_roll_length,
            'productivity' => (float)$this->productivity,
            'descr' => $this->description,
        ];
        //        return parent::toArray($request);
    }
}
