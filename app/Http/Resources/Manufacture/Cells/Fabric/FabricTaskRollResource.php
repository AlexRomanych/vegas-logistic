<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Carbon\Carbon;
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
            'status_prev' => $this->roll_status_previous,
            'start_at' => $this->start_at, // ? Carbon::parse($this->start_at)->format('d.m.Y H:i:s') : null,
            'paused_at' => $this->paused_at,
            'resume_at' => $this->resume_at,
            'finish_at' => $this->finish_at,
            'duration' => $this->duration,
            'finish_by' => $this->finish_by,
            'rolling' => $this->rolling,
            'textile_length' => (float)$this->textile_roll_length,
            'productivity' => (float)$this->productivity,
            'false_reason' => $this->false_reason,
            'rate' => (float)$this->translate_rate,
            'descr' => $this->description,
//            'user' => $this->user->name,
        ];
        //        return parent::toArray($request);
    }
}
