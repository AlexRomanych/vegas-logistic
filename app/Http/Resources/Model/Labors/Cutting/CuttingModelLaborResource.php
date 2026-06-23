<?php
/** @noinspection PhpUndefinedFieldInspection */

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
            'code_1c'           => $this->code_1c,
            'name'              => $this->name,
            'name_report'       => $this->name_report,
            'angle'             => $this->angle,
            'kdch'              => $this->kdch,
            'machine'           => $this->machine_type_name,
            'kdch_doc'          => $this->whenLoaded('kdchDoc', fn() => $this->kdchDoc),
            'cutting_schema_id' => $this->cuttingSchema->id,
            'cut_proc_up_id'    => $this->cuttingProcedureCoverUp->id,
            'cut_proc_down_id'  => $this->cuttingProcedureCoverDown->id,
            'cut_proc_side_id'  => $this->cuttingProcedureSide->id,
            'operations'        => CuttingOperationForSchemaResource::collection($this->cuttingOperations),
            // 'operations'       => $this->operations
            // '_' => parent::toArray($request)
        ];
    }
}
