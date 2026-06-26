<?php

namespace App\Http\Resources\Manufacture\Cells\Cutting\Textiles;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuttingTextileResource extends JsonResource
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
            'code_1c'     => $this->code_1c,
            'name'        => $this->name,
            'active'      => $this->active,
            'layers'      => $this->layers,
            'width'       => $this->width,
            'width_work'  => $this->width_work,
            'description' => $this->description,

            //'' => parent::toArray($request),
        ];
    }
}
