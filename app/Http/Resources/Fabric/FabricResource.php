<?php

namespace App\Http\Resources\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricResource extends JsonResource
{
    public static $wrap = 'fabric';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'code_1C' => $this->code_1C,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'pic' => $this->pic,
            'textile' => $this->textile,
            'active' => $this->active,
            'fillersList' => $this->fillersList,
            'fabric_machine' => [
                'id' => $this->fabricMachine->id,
                'name' => $this->fabricMachine->name,
                'short_name' => $this->fabricMachine->short_name
            ],
            'buffer' => (double)$this->buffer_amount,
            'optimal_party' => (double)$this->optimal_party,
            'description' => $this->description,
        ];
    }
}
