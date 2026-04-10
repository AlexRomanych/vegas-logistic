<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Model\Show;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelShowCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code_1c'     => $this->code_1c,
            'name'        => $this->name,
            'active'      => $this->active,
            'color'       => $this->color,
            'description' => $this->description,

            'models' => ModelShowResource::collection($this->whenLoaded('models')),

            //'note'        => $this->note,
            //'status'      => $this->status,
            //'comment'     => $this->comment,
            //'meta'        => $this->meta,
            //'created_at'  => $this->created_at,
            //'updated_at'  => $this->updated_at,


            //'_' => parent::toArray($request)
        ];
    }
}
