<?php
/** @noinspection ALL */

namespace App\Http\Resources\Model\Show;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelShowManufactureGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'group_number' => $this->group_number,

            //'' => parent::toArray($request)
        ];
    }
}
