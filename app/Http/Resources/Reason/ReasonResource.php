<?php

namespace App\Http\Resources\Reason;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReasonResource extends JsonResource
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
            'reason_number_in_reason_category' => $this->reason_number_in_reason_category,
            'reason_category_id' => $this->reason_category_id,
        ];

        // return parent::toArray($request);
    }
}
