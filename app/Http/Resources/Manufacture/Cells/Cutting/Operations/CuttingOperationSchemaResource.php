<?php

namespace App\Http\Resources\Manufacture\Cells\Cutting\Operations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuttingOperationSchemaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @noinspection PhpUndefinedFieldInspection
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'active'      => $this->active,
            'description' => $this->description,
            'operations'  => CuttingOperationForSchemaResource::collection($this->operations),
            // 'additional'  => $this->additional,
            // 'status'      => $this->status,
            // 'comment'     => $this->comment,
            // 'note'        => $this->note,
            // 'meta'        => $this->meta,
            // 'color'       => $this->color,
            // 'created_at'  => $this->created_at,
            // 'updated_at'  => $this->updated_at,

            // '_' => parent::toArray($request)
        ];
    }
}
