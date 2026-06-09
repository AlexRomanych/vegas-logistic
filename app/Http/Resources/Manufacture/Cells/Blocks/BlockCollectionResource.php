<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Manufacture\Cells\Blocks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'code_1c'     => $this->code_1c,
            'name'        => $this->name,
            'unit'        => $this->unit,
            'kdb'         => $this->kdb,
            'line'        => $this->line,
            'priority'    => $this->priority,
            'active'      => $this->active,
            'description' => $this->description,
            'height'      => $this->height,
            'blocks'      => BlockResource::collection($this->whenLoaded('blocks')),
            //'status'      => $this->status,
            //'comment'     => $this->comment,
            //'note'        => $this->note,
            //'meta'        => $this->meta,
            //'color'       => $this->color,
            //'created_at'  => $this->created_at,
            //'updated_at'  => $this->updated_at,

            //'_' => parent::toArray($request),
        ];
    }
}
