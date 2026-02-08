<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\Days;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingDayWorkerResource extends JsonResource
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
            'id'         => $this->id,
            'surname'    => $this->surname,
            'name'       => $this->name,
            'patronymic' => $this->patronymic,
            'pivot'      => $this->whenLoaded('pivot', function () {
                return [
                    'id'           => $this->pivot->id,
                    'working_time' => $this->pivot->working_time,
                ];
            }),
            // 'pivot'      => $this->pivot,

            // 'phone'        => $this->phone,
            // 'address'      => $this->address,
            // 'role'         => $this->role,
            // 'status'       => $this->status,
            // 'cell_items'   => $this->cell_items,
            // 'created_at'   => $this->created_at,
            // 'updated_at'   => $this->updated_at,
            // 'cell_item_id' => $this->cell_item_id,


            // '_' => parent::toArray($request)
        ];
    }
}
