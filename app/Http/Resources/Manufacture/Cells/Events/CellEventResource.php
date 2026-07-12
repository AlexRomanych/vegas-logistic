<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Manufacture\Cells\Events;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CellEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'cell'        => $this->cell,
            'day_id'      => $this->day_id,
            'start_at'    => $this->start_at?->format(RETURN_DATE_TIME_FORMAT),
            'finish_at'   => $this->finish_at?->format(RETURN_DATE_TIME_FORMAT),
            'event'       => $this->event,
            'answer'      => $this->answer,
            'created_at'  => $this->created_at?->format(RETURN_DATE_TIME_FORMAT),
            'updated_at'  => $this->updated_at?->format(RETURN_DATE_TIME_FORMAT),
            //'task_id'     => $this->task_id,
            //'category'    => $this->category,
            //'active'      => $this->active,
            //'status'      => $this->status,
            //'description' => $this->description,
            //'comment'     => $this->comment,
            //'note'        => $this->note,
            //'meta'        => $this->meta,
            //'color'       => $this->color,


            //'_' => parent::toArray($request)
        ];
    }
}
