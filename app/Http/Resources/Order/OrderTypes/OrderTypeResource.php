<?php

namespace App\Http\Resources\Order\OrderTypes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTypeResource extends JsonResource
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
            'id'           => $this->id,
            'name'         => $this->name,
            'display_name' => $this->display_name,
            'type_index'   => $this->type_index,
            'color'        => $this->color,
            'active'       => $this->active,
            'description'  => $this->description,
            'comment'      => $this->comment,
            // 'note'         => $this->note,
            // 'status'       => $this->status,
            // 'meta'         => $this->meta,
            // 'created_at'   => $this->created_at,
            // 'updated_at'   => $this->updated_at,

            // '_' => parent::toArray($request),
        ];
    }
}
