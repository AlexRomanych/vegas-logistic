<?php

namespace App\Http\Resources\Model;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    public static $wrap = 'model';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->code1C,
            'type' => $this->type,
            'name' => $this->name,
            'coll' => $this->collection->name,
            'bh' => $this->base_height * 100,
            'ch' => $this->cover_height * 100,
            'textile' => $this->textile,
            'bc' => $this->base_composition,

        ];
    }
}
