<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingTaskOrderClientResource extends JsonResource
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
            'add_name'    => $this->add_name,
            'short_name'  => $this->short_name,

            // 'region'      => $this->region,
            // 'country'     => $this->country,
            // 'address'     => $this->address,
            // 'longitude'   => $this->longitude,
            // 'latitude'    => $this->latitude,
            // 'created_at'  => $this->created_at,
            // 'updated_at'  => $this->updated_at,
            // 'manager_id'  => $this->manager_id,
            // 'active'      => $this->active,
            // 'description' => $this->description,
            // 'comment'     => $this->comment,
            // 'note'        => $this->note,
            // 'meta'        => $this->meta,
            // 'code_1c'     => $this->code_1c,

            // '_'           => parent::toArray($request),
        ];
    }
}
