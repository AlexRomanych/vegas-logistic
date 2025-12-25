<?php

namespace App\Http\Resources\Order\Render;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderLineRenderResource extends JsonResource
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
            'size'        => $this->size,
            'amount'      => $this->amount,
            'textile'     => $this->textile,
            'composition' => $this->composition,
            'describe_1'  => $this->describe_1,
            'describe_2'  => $this->describe_2,
            'describe_3'  => $this->describe_3,
            'model'       => new OrderLineModelRenderResource($this->model),
            // 'model_code_1c' => $this->model_code_1c,
            // 'model_name'  => $this->model_name,

            // 'width'         => $this->width,
            // 'length'        => $this->length,
            // 'height'        => $this->height,
            // 'order_id'      => $this->order_id,
            // 'active'        => $this->active,
            // 'status'        => $this->status,
            // 'description'   => $this->description,
            // 'comment'       => $this->comment,
            // 'note'          => $this->note,
            // 'meta'          => $this->meta,
            // 'created_at'    => $this->created_at,
            // 'updated_at'    => $this->updated_at,


            // '_' => parent::toArray($request)
        ];
    }
}
