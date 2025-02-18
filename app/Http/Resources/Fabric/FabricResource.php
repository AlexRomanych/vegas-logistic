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
            'pic' => $this->pic,
            'textile' => $this->textile,
            'fillersList' => $this->fillersList,
            'fabric_machine' => [
                'name' => $this->fabricMachine->name,
                'short_name' => $this->fabricMachine->short_name
            ],
            'buffer' => (double)$this->buffer_amount,
            'optimal_party' => (double)$this->optimal_patry,
        ];
    }
}
