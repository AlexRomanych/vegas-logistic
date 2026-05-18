<?php

namespace App\Http\Resources\Manufacture\Cells\Cutting\Operations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuttingOperationForSchemaResource extends JsonResource
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
            'pivot' => [
                'amount' => $this->pivot->amount,
                'ratio' => $this->pivot->ratio,
                'position' => $this->pivot->position,
                'condition' => $this->pivot->condition,
            ],

            // '_' => parent::toArray($request)
        ];
    }
}
