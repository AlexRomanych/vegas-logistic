<?php

namespace App\Http\Resources\Manufacture\Cells\Cutting\CuttingTaskManage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuttingTaskOrderLineResource extends JsonResource
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
            'id'   => $this->id,
            'size' => $this->size,
            'dims' => [
                'width'  => $this->width,
                'length' => $this->length,
                'height' => $this->height,
            ],

            'model' => [
                'main'  => (new CuttingTaskModelResource($this->whenLoaded('model')))
                    ->additional(['phantom_data' => $this->additional['phantom_data']]),

                // !!! Используем $this->model->getRelation('cover' и 'base'), потому что есть поле $this->model->cover и $this->model->base,
                'base'  => new CuttingTaskModelResource($this->model->getRelation('base')),
                'cover' => new CuttingTaskModelResource($this->model->getRelation('cover')),
                // 'cover' => new CuttingTaskModelResource($this->whenLoaded('cover')),
                // 'base' => new CuttingTaskModelResource($this->whenLoaded('base')),
                // 'cover' => $this->model->getRelation('cover'),
            ],

            'amount'      => $this->amount,
            'textile'     => $this->textile,
            'composition' => $this->composition,
            'describe_1'  => $this->describe_1,
            'describe_2'  => $this->describe_2,
            'describe_3'  => $this->describe_3,

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
