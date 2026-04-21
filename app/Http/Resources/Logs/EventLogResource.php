<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Logs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'level'      => $this->level,
            'target'     => $this->target,
            'message'    => $this->message,
            'context'    => $this->context,
            'created_at' => $this->created_at?->format(RETURN_DATE_TIME_FORMAT),
            //'updated_at' => $this->updated_at?->format(RETURN_DATE_TIME_FORMAT),

            //'_' => parent::toArray($request)
        ];
    }
}
