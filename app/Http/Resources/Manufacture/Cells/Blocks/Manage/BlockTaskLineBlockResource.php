<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Manufacture\Cells\Blocks\Manage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockTaskLineBlockResource extends JsonResource
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
            'width'       => $this->width,
            'length'      => $this->length,
            'active'      => $this->active,
            'description' => $this->description,
            'collection'  => $this->whenLoaded('blockCollection', fn($blockCollection) => new BlockTaskLineBlockCollectionResource($blockCollection)),
            //'status'      => $this->status,
            //'comment'     => $this->comment,
            //'note'        => $this->note,
            //'meta'        => $this->meta,
            //'color'       => $this->color,
            //'created_at'  => $this->created_at,
            //'updated_at'  => $this->updated_at,
            //'collection'  => $this->collection,
            //'height'      => $this->height,

            //'_' => parent::toArray($request),
        ];
    }
}
