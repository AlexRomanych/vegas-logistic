<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\Statuses;

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
            'id'          => $this->id,
            'name'        => $this->name,
            'color'       => $this->color,
            'position'    => $this->position,
            'description' => $this->description,

            // 'active'      => $this->active,
            // 'status'      => $this->status,
            // 'comment'     => $this->comment,
            // 'note'        => $this->note,
            // 'meta'        => $this->meta,
            // 'created_at'  => $this->created_at ? Carbon::parse($this->created_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            // 'updated_at'  => $this->updated_at ? Carbon::parse($this->updated_at)->format(RETURN_DATE_TIME_FORMAT) : null,

            // '_' => parent::toArray($request),
        ];
    }
}
