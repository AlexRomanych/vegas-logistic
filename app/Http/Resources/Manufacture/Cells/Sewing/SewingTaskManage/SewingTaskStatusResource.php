<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingTaskStatusResource extends JsonResource
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

            'id'    => $this->id,
            'name'  => $this->name,
            'color' => $this->color,
            //'comment'     => $this->comment,
            // 'description' => $this->description,
            // 'active'      => $this->active,
            // 'note'        => $this->note,
            // 'position'    => $this->position,
            // 'status'      => $this->status,
            // 'created_at'  => $this->created_at ? Carbon::parse($this->created_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            // 'updated_at'  => $this->updated_at ? Carbon::parse($this->updated_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            // 'meta'        => $this->meta,

            'pivot' => [
                'set_at'      => $this->pivot->set_at ? Carbon::parse($this->pivot->set_at)->format(RETURN_DATE_TIME_FORMAT) : null,
                'started_at'  => $this->pivot->started_at ? Carbon::parse($this->pivot->started_at)->format(RETURN_DATE_TIME_FORMAT) : null,
                'finished_at' => $this->pivot->finished_at ? Carbon::parse($this->pivot->finished_at)->format(RETURN_DATE_TIME_FORMAT) : null,
                'duration'    => $this->pivot->duration,
                'created_at'  => $this->pivot->created_at ? Carbon::parse($this->pivot->created_at)->format(RETURN_DATE_TIME_FORMAT) : null,

                // 'sewing_task_id'        => $this->pivot->sewing_task_id,
                // 'sewing_task_status_id' => $this->pivot->sewing_task_status_id,

            ],

            // '_' => parent::toArray($request),
        ];
    }
}


