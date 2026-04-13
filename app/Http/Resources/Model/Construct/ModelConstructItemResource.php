<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Model\Construct;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelConstructItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            // __ Проверяем: была ли запрошена связь через with()
            'material' => $this->relationLoaded('material')
                ? ($this->material
                    ? new ModelConstructMaterialResource($this->material)   // __ Если связь есть и не null
                    : [                                                     // __ Подменяем ресурс на массив, если null
                        'code_1c'      => $this->material_code_1c_copy,
                        //'code_1c_copy' => $this->material_code_1c_copy,
                        'name'         => $this->material_name,
                        'unit'         => $this->unit,
                        'in_base'      => false,  // __ Подменяем ресурс на массив, если null (наличие в таблице материалов)
                    ])                                               // __ Если в БД там null
                : $this->whenLoaded('material'),        // __ Если связь вообще не грузили (ключ удалится)

            //'material' => new ModelConstructMaterialResource($this->whenLoaded('material')),

            'detail_height' => $this->detail_height,
            'detail'        => $this->detail,
            'procedure'     => new ModelConstructProcedureResource($this->whenLoaded('procedure')),
            'amount'        => $this->amount,
            'position'      => $this->position,

            //'id'                     => $this->id,
            //'construct_code_1c'      => $this->construct_code_1c,
            //'material_code_1c'       => $this->material_code_1c,
            //'material_code_1c_copy'  => $this->material_code_1c_copy,
            //'material_name'          => $this->material_name,
            //'material_unit'          => $this->material_unit,
            //'procedure_code_1c'      => $this->procedure_code_1c,
            //'procedure_code_1c_copy' => $this->procedure_code_1c_copy,
            //'procedure_name'         => $this->procedure_name,
            //'active'                 => $this->active,
            //'status'                 => $this->status,
            //'description'            => $this->description,
            //'comment'                => $this->comment,
            //'note'                   => $this->note,
            //'meta'                   => $this->meta,
            //'color'                  => $this->color,
            //'created_at'             => $this->created_at,
            //'updated_at'             => $this->updated_at,

            //'_' => parent::toArray($request)
        ];
    }
}
