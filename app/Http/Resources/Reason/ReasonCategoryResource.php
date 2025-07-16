<?php

namespace App\Http\Resources\Reason;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReasonCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'active' => $this->active,
            'description' => $this->description,
            'group_number_in_cell_group' => $this->group_number_in_cell_group,
            'cells_group_id' => $this->cells_group_id,
            'reasons' => ReasonResource::collection($this->whenLoaded('reasons')),
        ];
//        return parent::toArray($request);
    }
}
