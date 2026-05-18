<?php

namespace App\Http\Resources\Manufacture\Cells\Cutting\Days;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuttingDayResource extends JsonResource
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
            'action_at'     => $this->action_at?->format(RETURN_DATE_TIME_FORMAT),
            'action_at_str' => $this->action_at_str,
            'change'        => $this->change,
            'start_at'      => $this->start_at?->format(RETURN_DATE_TIME_FORMAT),
            'paused_at'     => $this->paused_at?->format(RETURN_DATE_TIME_FORMAT),
            'resume_at'     => $this->resume_at?->format(RETURN_DATE_TIME_FORMAT),
            'finish_at'     => $this->finish_at?->format(RETURN_DATE_TIME_FORMAT),
            'duration'      => $this->duration ?? 0,
            'description'   => $this->description,
            'comment'       => $this->comment,
            'ready'         => $this->ready,
            'responsible'   => new CuttingDayWorkerResource($this->whenLoaded('responsible')),

            // __ Отправляем только активных рабочих
            'workers'       => CuttingDayWorkerResource::collection($this->whenLoaded('workers')),
            // 'workers'       => CuttingDayWorkerResource::collection($this->whenLoaded('activeWorkers')),

            // 'workers'       => CuttingDayWorkerResource::collection($this->whenLoaded('workers')),
            // 'responsible'   => new CuttingDayWorkerResource($this->responsible),
            // 'meta_ext'       => $this->meta_ext,
            // 'active'         => $this->active,
            // 'status'         => $this->status,
            // 'note'           => $this->note,
            // 'meta'           => $this->meta,
            // 'color'          => $this->color,
            // 'created_at'     => $this->created_at,
            // 'updated_at'     => $this->updated_at,

            // '_' => parent::toArray($request)

        ];
    }
}
