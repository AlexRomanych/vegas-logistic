<?php

namespace App\Http\Resources\Worker;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
{
    public static $wrap = 'workers';

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
            'surname' => $this->surname,
            'patronymic' => $this->patronymic,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
            'name' => $this->name,
            'cell_item_id' => $this->cellItem->id,
            'cell_item_name' => $this->cellItem->name
        ];
    }
}
