<?php

namespace App\Http\Resources\Model\Labors;

use App\Http\Resources\Manufacture\Cells\Sewing\Operations\SewingOperationForSchemaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelLaborResource extends JsonResource
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
            'code_1c'          => $this->code_1c,
            'name'             => $this->name,
            'name_report'      => $this->name_report,
            'sewing_schema_id' => $this->sewingSchema->id,
            'operations'       => SewingOperationForSchemaResource::collection($this->operations),
            // 'operations'       => $this->operations
            // '_' => parent::toArray($request)
        ];
    }
}
