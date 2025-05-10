<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CLientFabricOrderResource extends JsonResource
{
    // Descr: Клиент, который отдаем для заказо в расходе ПС

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'active' => $this->active,
            'short_name' => $this->short_name,
//            'region' => $this->region,
//            'name' => $this->name,
//            'longitude' => $this->longitude,
//            'latitude' => $this->latitude,
//            'description' => $this->description,
//            'country' => $this->country,
//            'address' => $this->address,
//            'add_name' => $this->add_name,
//            'id' => $this->id,
        ];

//        return parent::toArray($request);
    }
}
