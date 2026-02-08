<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\Days;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingDayResource extends JsonResource
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
            'action_at'     => $this->action_at,
            'action_at_str' => $this->action_at_str,
            'change'        => $this->change,
            'start_at'      => $this->start_at,
            'paused_at'     => $this->paused_at,
            'resume_at'     => $this->resume_at,
            'finish_at'     => $this->finish_at,
            'duration'      => $this->duration,
            'description'   => $this->description,
            'responsible'   => new SewingDayWorkerResource($this->whenLoaded('responsible')),
            'workers'       => SewingDayWorkerResource::collection($this->whenLoaded('workers')),

            // 'responsible'   => new SewingDayWorkerResource($this->responsible),
            // 'meta_ext'       => $this->meta_ext,
            // 'active'         => $this->active,
            // 'status'         => $this->status,
            // 'comment'        => $this->comment,
            // 'note'           => $this->note,
            // 'meta'           => $this->meta,
            // 'color'          => $this->color,
            // 'created_at'     => $this->created_at,
            // 'updated_at'     => $this->updated_at,

            // '_' => parent::toArray($request)
        ];
    }
}
