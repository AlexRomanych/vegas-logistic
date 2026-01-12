<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingTaskOrderLineResource extends JsonResource
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
            'id'            => $this->id,
            'size'          => $this->size,
            'dims'          => [
                'width'         => $this->width,
                'length'        => $this->length,
                'height'        => $this->height,
            ],

            'model' => [
                'main' => new SewingTaskModelResource($this->model),
                // !!! Используем $this->model->getRelation('cover' и 'base'), потому что есть поле $this->model->cover и $this->model->base,
                'base' => new SewingTaskModelResource($this->model->getRelation('base')),
                'cover' => new SewingTaskModelResource($this->model->getRelation('cover')),
                // 'cover' => $this->model->getRelation('cover'),
            ],

            'amount'        => $this->amount,
            'textile'       => $this->textile,
            'composition'   => $this->composition,
            'describe_1'    => $this->describe_1,
            'describe_2'    => $this->describe_2,
            'describe_3'    => $this->describe_3,

            // 'model_name'    => $this->model_name,
            // 'model_code_1c' => $this->model_code_1c,
            // 'stat_include'  => $this->stat_include,
            // 'order_id'      => $this->order_id,
            // 'active'        => $this->active,
            // 'status'        => $this->status,
            // 'description'   => $this->description,
            // 'comment'       => $this->comment,
            // 'note'          => $this->note,
            // 'meta'          => $this->meta,
            // 'color'         => $this->color,
            // 'created_at'    => $this->created_at,
            // 'updated_at'    => $this->updated_at,

            // '_' => parent::toArray($request),
        ];
    }
}
