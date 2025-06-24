<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricPictureSchemaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'schema_name' => $this->schema_name,
            'schema' => $this->schema,
            'description' => $this->description ?? '',
        ];

//        return parent::toArray($request);
    }
}
