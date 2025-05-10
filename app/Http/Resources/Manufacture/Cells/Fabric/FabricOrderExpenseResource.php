<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricOrderExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'expense' => (float)$this->expense,
            'fabric_id' => $this->fabric_id,
//            'fabric_order_id' => $this->fabric_order_id,
//            'name' => $this->name,
//            'description' => $this->description,
//            'id' => $this->id,
//            'expense_at' => $this->expense_at,

        ];

//        return parent::toArray($request);
    }
}
