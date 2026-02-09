<?php

namespace App\Http\Resources\Worker;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
{
    // public static $wrap = 'worker';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    public function toArray(Request $request): array
    {

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'active'      => $this->active,
            'surname'     => $this->surname,
            'patronymic'  => $this->patronymic,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'description' => $this->description,
            'comment'     => $this->comment,
            'cell_item'   => $this->whenLoaded('cellItem', function () {
                return [
                    'id'   => $this->cellItem->id,
                    'name' => $this->cellItem->name,
                    'no'   => $this->cellItem->no,
                ];
            }),
            // 'record_id'  => $this->record_id ?? 0,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
            // 'cell_item_id' => $this->cellItem->id,
            // 'cell_item_name' => $this->cellItem->name

            // '_' => parent::toArray($request),
            'address'     => $this->address,
        ];
    }
}
