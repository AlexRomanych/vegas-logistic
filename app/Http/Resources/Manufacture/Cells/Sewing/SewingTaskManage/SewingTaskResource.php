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

        // __ Получаем последний статус из уже загруженной коллекции (если statuses есть)
        // __ Получаем текущий статус, сортируя по дате в ПИВОТ-таблице
        $currentStatus = $this->whenLoaded('statuses', function () {
            return $this->statuses->sortByDesc(function ($status) {
                return $status->pivot->id;
            })->first();

            // return $this->statuses->sortByDesc(function ($status) {
            //     // Сортируем по полю created_at внутри промежуточной таблицы
            //     return $status->pivot->created_at;
            // })->first();
        });

        /** @noinspection PhpUndefinedFieldInspection */
        return [

            'id'        => $this->id,
            'id_ref'    => $this->id,
            'action_at' => Carbon::parse($this->action_at)->format(RETURN_DATE_TIME_FORMAT),
            'position'  => $this->position,
            'change'    => $this->change,
            'active'    => $this->active,
            'comment'   => $this->comment,

            'order' => new SewingTaskOrderResource($this->whenLoaded('order')),

            'sewing_lines' => SewingTaskLineResource::collection($this->whenLoaded('sewingLines')), // 'sewing_lines

            'statuses'       => SewingTaskStatusResource::collection($this->whenLoaded('statuses')),
            'current_status' => $currentStatus ? new SewingTaskStatusResource($currentStatus) : null,


            // 'order_id'    => $this->order_id,
            // 'meta_ext'    => $this->meta_ext,
            // 'status'      => $this->status,
            // 'description' => $this->description,

            // 'note'        => $this->note,
            // 'meta'        => $this->meta,
            // 'color'       => $this->color,
            // 'created_at'  => $this->created_at,
            // 'updated_at'  => $this->updated_at,

            // '_' => parent::toArray($request),
        ];
    }
}
