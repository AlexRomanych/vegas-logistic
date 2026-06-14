<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Order\Render;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderLineMaterialRenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'code_1c'         => $this->code_1c,
            'name'            => $this->name,
            'unit'            => $this->unit,
            'expense_per_pic' => $this->pivot->expense_per_pic,
            'rest_per_pic'    => $this->pivot->rest_per_pic,

            //'pivot'   => [
            //    'expense_per_pic' => $this->pivot->expense_per_pic,
            //    'rest_per_pic'    => $this->pivot->rest_per_pic,
            //],

            //'material_group_code_1c'    => $this->material_group_code_1c,
            //'material_category_code_1c' => $this->material_category_code_1c,
            //'code_1c_copy'              => $this->code_1c_copy,
            //'supplier'                  => $this->supplier,
            //'alt_unit'                  => $this->alt_unit,
            //'alt_multiplier'            => $this->alt_multiplier,
            //'apply_alt_unit'            => $this->apply_alt_unit,
            //'order'                     => $this->order,
            //'is_deleted'                => $this->is_deleted,
            //'is_shown'                  => $this->is_shown,
            //'is_collapsed'              => $this->is_collapsed,
            //'is_checked'                => $this->is_checked,
            //'deleted_at'                => $this->deleted_at,
            //'meta_extended'             => $this->meta_extended,
            //'active'                    => $this->active,
            //'status'                    => $this->status,
            //'description'               => $this->description,
            //'comment'                   => $this->comment,
            //'note'                      => $this->note,
            //'meta'                      => $this->meta,
            //'color'                     => $this->color,
            //'created_at'                => $this->created_at,
            //'updated_at'                => $this->updated_at,
            //'object_name'               => $this->object_name,
            //'object_code_1c'            => $this->object_code_1c,
            //'properties'                => $this->properties,
            //'is_modify'                 => $this->is_modify,
            //'ext_meta'                  => $this->ext_meta,

            //'_' => parent::toArray($request)
        ];
    }
}
