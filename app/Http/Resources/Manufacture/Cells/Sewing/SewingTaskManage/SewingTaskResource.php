<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingTaskResource extends JsonResource
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

            'id'        => $this->id,
            'action_at' => Carbon::parse($this->action_at)->format(RETURN_DATE_TIME_FORMAT),
            'position'  => $this->position,
            'change'    => $this->change,
            'active'    => $this->active,

            'order' => new SewingTaskOrderResource($this->whenLoaded('order')),

            'sewing_lines' => SewingTaskLineResource::collection($this->whenLoaded('sewingLines')), // 'sewing_lines

            'statuses' => SewingTaskStatusResource::collection($this->whenLoaded('statuses')),

            // 'order_id'    => $this->order_id,
            // 'meta_ext'    => $this->meta_ext,
            // 'status'      => $this->status,
            // 'description' => $this->description,
            // 'comment'     => $this->comment,
            // 'note'        => $this->note,
            // 'meta'        => $this->meta,
            // 'color'       => $this->color,
            // 'created_at'  => $this->created_at,
            // 'updated_at'  => $this->updated_at,

            // '_' => parent::toArray($request),
        ];
    }
}
