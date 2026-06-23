<?php

namespace App\Http\Resources\Order\Render;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRenderCuttingTaskLinesResource extends JsonResource
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
            'table'            => $this->table,
            'fabric_construct' => $this->fabric_construct,
            'cut_length'       => $this->cut_length,
            'cut_width'        => $this->cut_width,
            'angle'            => $this->angle,
            'is_panel'         => $this->is_panel,
            'is_side'          => $this->is_side,
            'time'             => $this->time,
            'detail'           => $this->detail,
            'expense'          => $this->expense,
            'cut'              => $this->cut,
            'finished_at'      => $this->finished_at,
            'false_at'         => $this->false_at,
            //'cover_height'     => $this->cover_height,
            //'cutting_task_id'  => $this->cutting_task_id,
            //'order_line_id'    => $this->order_line_id,
            //'parent_id'        => $this->parent_id,
            //'phantom'          => $this->phantom,
            //'phantom_json'     => $this->phantom_json,
            //'position'         => $this->position,
            //'finished_by'      => $this->finished_by,
            //'false_reason'     => $this->false_reason,
            //'false_history'    => $this->false_history,
            //'has_panel'        => $this->has_panel,
            //'has_side'         => $this->has_side,
            //'time_json'        => $this->time_json,
            //'active'           => $this->active,
            //'status'           => $this->status,
            //'description'      => $this->description,
            //'comment'          => $this->comment,
            //'note'             => $this->note,
            //'meta'             => $this->meta,
            //'color'            => $this->color,
            //'created_at'       => $this->created_at,
            //'updated_at'       => $this->updated_at,
            //'position_day'     => $this->position_day,
            //'_' => parent::toArray($request)
        ];
    }
}
