<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Material;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialShowMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'code_1c'     => $this->code_1c,
            'name'        => $this->name,
            'active'      => $this->active,
            'is_shown'    => $this->is_shown,
            'order'       => $this->order,
            'unit'        => $this->unit,
            'alt_unit'    => $this->alt_unit,
            'object_name' => $this->object_name,
            'properties'  => $this->properties,
            'supplier'    => $this->supplier,
            'description' => $this->description,

            //'material_group_code_1c'    => $this->material_group_code_1c,
            //'material_category_code_1c' => $this->material_category_code_1c,
            //'code_1c_copy'              => $this->code_1c_copy,
            //'alt_multiplier'            => $this->alt_multiplier,
            //'apply_alt_unit'            => $this->apply_alt_unit,
            //'status'                    => $this->status,
            //'comment'                   => $this->comment,
            //'note'                      => $this->note,
            //'is_checked'                => $this->is_checked,
            //'meta'                      => $this->meta,
            //'color'                     => $this->color,
            //'deleted_at'                => $this->deleted_at,
            //'created_at'                => $this->created_at,
            //'updated_at'                => $this->updated_at,
            //'meta_extended'             => $this->meta_extended,
            //'is_collapsed'              => $this->is_collapsed,
            //'is_deleted'                => $this->is_deleted,


            //'_' => parent::toArray($request),
        ];
    }
}
