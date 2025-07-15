<?php

namespace App\Http\Resources\Reason;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CellItemResource extends JsonResource
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
            'reason_categories' => ReasonCategoryResource::collection($this->whenLoaded('reasonCategories')),
            // 'reason_categories' => ReasonCategoryResource::collection($this->reasonCategories),
        ];
        // return parent::toArray($request);
    }
}
