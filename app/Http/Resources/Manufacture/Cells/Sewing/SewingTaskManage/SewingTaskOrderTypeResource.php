<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingTaskOrderTypeResource extends JsonResource
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
            'id'           => $this->id,
            'display_name' => $this->display_name,
            'color'        => $this->color,

            // '_'            => parent::toArray($request),
        ];
    }
}
