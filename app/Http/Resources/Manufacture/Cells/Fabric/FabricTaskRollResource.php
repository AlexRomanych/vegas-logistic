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
            'registration_1C_at' => $this->registration_1C_at,
            'move_to_cut_at' => $this->move_to_cut_at,
            'receipt_to_cut_at' => $this->receipt_to_cut_at,
            'close_at' => 'n/a',
            'duration' => $this->duration,
            'rolling' => $this->rolling,
            'textile_length' => (float)$this->textile_roll_length,
            'fabric_length' => (float)$this->fabric_roll_length,
            'productivity' => (float)$this->productivity,
            'false_reason' => $this->false_reason,
            'movable' => $this->movable,
            'rate' => (float)$this->translate_rate,
            'descr' => $this->description,
            'rolling_length' => 0,
            'responsible' => [
                'finishBy' => $this->finishBy,
                'register1CBy' => $this->registration1CBy,
                'moveToCutBy' => $this->moveToCutBy,
                'receiptToCutBy' => $this->receiptToCutBy,
                'closeBy' => 'n/a',
            ],
            'finish_by' => $this->finish_by,
            // 'rolling_length' => (float)$this->textile_rolling_length,
//            'user' => $this->user->name,
        ];
        //        return parent::toArray($request);
    }
}
