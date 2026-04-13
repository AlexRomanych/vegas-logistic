<?php
/** @noinspection ALL */

namespace App\Http\Resources\Model\Construct;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelConstructResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code_1c' => $this->code_1c,
            'name'    => $this->name,

            'items' => ModelConstructItemResource::collection($this->whenLoaded('constructItems')),

            //'model_code_1c' => $this->model_code_1c,
            //'model_name'    => $this->model_name,
            //'type'          => $this->type,
            //'active'        => $this->active,
            //'status'        => $this->status,
            //'description'   => $this->description,
            //'comment'       => $this->comment,
            //'note'          => $this->note,
            //'meta'          => $this->meta,
            //'color'         => $this->color,
            //'created_at'    => $this->created_at,
            //'updated_at'    => $this->updated_at,

            //' ' => parent::toArray($request)
        ];
    }
}
