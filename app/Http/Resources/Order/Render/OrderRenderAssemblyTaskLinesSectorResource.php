<?php

namespace App\Http\Resources\Order\Render;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRenderAssemblyTaskLinesSectorResource extends JsonResource
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
            'id'               => $this->id,
            'sector'           => $this->sector,
            'detail_width'     => $this->detail_width,
            'detail_length'    => $this->detail_length,
            'detail_height'    => $this->detail_height,
            'material_code_1c' => $this->material_code_1c,
            'material_name'    => $this->material_name,

            'expense' => $this->expense,
            'rest'    => $this->rest,
            'total'   => $this->total,

            'expense_per_pic' => $this->expense_per_pic,
            'rest_per_pic'    => $this->rest_per_pic,
            'total_per_pic'   => $this->total_per_pic,

            'amount' => $this->amount,
            'count'  => $this->count,
            'time'   => $this->time,

            'description'           => $this->description,

            //'comment'               => $this->comment,
            //'note'                  => $this->note,
            //'meta'                  => $this->meta,
            //'color'                 => $this->color,
            //'created_at'            => $this->created_at,
            //'updated_at'            => $this->updated_at,
            //'order_line_id'         => $this->order_line_id,
            //'assembly_task_line_id' => $this->assembly_task_line_id,


            //'status'                => $this->status,
            //'active'                => $this->active,
            //'outputs'               => $this->outputs,
            //'time_json'             => $this->time_json,
            //'false_history'         => $this->false_history,
            //'false_reason'          => $this->false_reason,
            //'finished_by'           => $this->finished_by,
            //'false_at'              => $this->false_at,
            //'finished_at'           => $this->finished_at,
            //'position_day'          => $this->position_day,
            //'position'              => $this->position,
            //'phantom_json'          => $this->phantom_json,
            //'phantom'               => $this->phantom,
            //'height'                => $this->height,
            //'length'                => $this->length,
            //'width'                 => $this->width,

            //'_' => parent::toArray($request)
        ];
    }
}
