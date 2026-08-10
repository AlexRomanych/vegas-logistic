<?php

namespace App\Http\Resources\Order\Render;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRenderAssemblyTaskLinesResource extends JsonResource
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
            'amount'           => $this->amount,
            'finished_at'      => $this->finished_at,
            'false_at'         => $this->false_at,
            'time'             => $this->time,

            'sectors'          => $this->whenLoaded('sectors', fn() => OrderRenderAssemblyTaskLinesSectorResource::collection($this->sectors)),

            //'finished_by'      => $this->finished_by,
            //'false_reason'     => $this->false_reason,
            //'false_history'    => $this->false_history,
            //'position'         => $this->position,
            //'position_day'     => $this->position_day,
            //'time_json'        => $this->time_json,
            //'phantom'          => $this->phantom,
            //'phantom_json'     => $this->phantom_json,
            //'active'           => $this->active,
            //'status'           => $this->status,
            //'description'      => $this->description,
            //'comment'          => $this->comment,
            //'note'             => $this->note,
            //'meta'             => $this->meta,
            //'color'            => $this->color,
            //'created_at'       => $this->created_at,
            //'updated_at'       => $this->updated_at,
            //'assembly_task_id' => $this->assembly_task_id,
            //'order_line_id'    => $this->order_line_id,

            //'_' => parent::toArray($request)
        ];
    }
}
