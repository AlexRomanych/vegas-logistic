<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// ___ Этот ресурс будет использоваться для возвращения данных для основного
// ___ запроса получения списка СЗ на дату
class FabricTaskContextResourceDate extends JsonResource
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
            'rate' => (float)$this->translate_rate,
            'average_textile_length' => (float)$this->average_textile_length,
            'average_fabric_length' => $this->average_fabric_length,
            'average_textile_roll_length' => $this->average_textile_roll_length,
            // 'average_fabric_length' => (float)$this->average_textile_length/(float)$this->translate_rate,
            'productivity' => (float)$this->productivity,
            'buffer' => (float)$this->fabric->buffer_amount,
            'buffer_min' => (float)$this->fabric->buffer_min,
            'buffer_max' => (float)$this->fabric->buffer_max,
            'buffer_min_rolls' => (float)$this->fabric->buffer_min_rolls,
            'buffer_max_rolls' => (float)$this->fabric->buffer_max_rolls,
            'rolls_amount' => $this->rolls_amount,
            'length_amount' => $this->rolls_amount * $this->average_textile_length,
            'fabric_id' => $this->fabric_id,
            'fabric' => $this->fabric->display_name,
            'fabric_rate' => (float)$this->fabric->translate_rate,
            'fabric_mode' => $this->fabric_mode,
            'textile_layers_amount' => $this->fabric->textile_layers_amount,
            'descr' => $this->description,
            'note' => $this->note,
            'correct' => true,
            'editable' => $this->editable,
            'rolls_exec' => FabricTaskRollResource::collection($this->fabricTaskRolls),
            'roll_position' => $this->roll_position,
        ];
        //        return parent::toArray($request);
    }
}

