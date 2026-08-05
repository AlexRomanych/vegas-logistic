<?php

namespace App\Http\Resources\Manufacture\Cells\Assembly\ManufactureGroups;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssemblyModelManufactureGroupResource extends JsonResource
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
            'group_number' => $this->group_number,
            'active'       => $this->active,
            'description'  => $this->description,
            'color'        => $this->color,

            'status'       => $this->status,
            'comment'      => $this->comment,
            'note'         => $this->note,
            'meta'         => $this->meta,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,

            //'_' => parent::toArray($request)
        ];
    }
}
