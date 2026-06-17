<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Order\Render;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class OrderRenderTaskStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'task_id'   => $this->id,
            'action_at' => $this->action_at?->format(RETURN_DATE_TIME_FORMAT),
            'status'    => $this->whenLoaded('currentStatus', fn() => [
                'status_id'    => $this->currentStatus->id,
                'display_name' => $this->currentStatus->name,
                'set_at'       => $this->currentStatus->set_at ? Carbon::parse($this->currentStatus->set_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            ]),
            'line_ids'  => $this->whenLoaded('lines', fn() => $this->lines->pluck('orderLine.id')->all()),
            //'line_ids'  => $this->whenLoaded('lines', fn() => $this->lines->modelKeys()),
            //'_'         => parent::toArray($request),
        ];
    }
}
