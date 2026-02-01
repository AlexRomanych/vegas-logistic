<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\Operations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingOperationResource extends JsonResource
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
            'id'          => $this->id,
            'name'        => $this->name,
            'machine'     => $this->machine,
            'type'        => $this->type,
            'time'        => $this->time,
            'description' => $this->description,
            'active'      => $this->active,
            'color'       => $this->color,
            // 'created_at'  => $this->created_at,
            // 'updated_at'  => $this->updated_at,
            // 'meta'        => $this->meta,
            // 'note'        => $this->note,
            // 'comment'     => $this->comment,
            // 'status'      => $this->status,
            // 'additional'  => $this->additional,

            // '_' => parent::toArray($request)
        ];
    }
}
