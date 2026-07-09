<?php

namespace App\Http\Resources\Manufacture\Cells\Blocks\Tuning;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockCollectionTuningTimeResource extends JsonResource
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
            'id'           => $this->id,
            'code_1c'      => $this->code_1c,
            'name'         => $this->name,
            'line'         => $this->line,
            'line_alt'     => $this->line_alt,
            'priority'     => $this->priority,

            'collections_to' => $this->whenLoaded('collectionsTo', fn () => self::collection($this->collectionsTo)),

            // __ ВЫВОД ДАННЫХ ИЗ PIVOT:
            // __ Используем $this->whenPivotLoaded для безопасности,
            // __ чтобы ресурс не падал, если модель загружена отдельно (не через связь)
            'tuning_time' => $this->whenPivotLoaded('block_tuning_times', function () {
                return $this->pivot->tuning_time;
            }),

            //'productivity' => $this->productivity,
            //'active'       => $this->active,
            //'status'       => $this->status,
            //'description'  => $this->description,
            //'comment'      => $this->comment,
            //'note'         => $this->note,
            //'meta'         => $this->meta,
            //'color'        => $this->color,
            //'created_at'   => $this->created_at,
            //'updated_at'   => $this->updated_at,
            //'kdb'          => $this->kdb,
            //'height'       => $this->height,
            //'length'       => $this->length,
            //'unit'         => $this->unit,
            //'own'          => $this->own,

            //'' => parent::toArray($request),
        ];
    }
}
