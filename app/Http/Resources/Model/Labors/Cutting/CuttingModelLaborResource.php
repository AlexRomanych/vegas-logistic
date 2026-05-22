<?php

namespace App\Http\Resources\Model\Labors\Cutting;

use App\Http\Resources\Manufacture\Cells\Cutting\Operations\CuttingOperationForSchemaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuttingModelLaborResource extends JsonResource
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
            'cutting_schema_id' => $this->cuttingSchema->id,
            'operations'       => CuttingOperationForSchemaResource::collection($this->cuttingOperations),
            // 'operations'       => $this->operations
            // '_' => parent::toArray($request)
        ];
    }
}
