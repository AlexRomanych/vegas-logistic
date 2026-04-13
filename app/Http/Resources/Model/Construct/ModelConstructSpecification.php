<?php /** @noinspection ALL */

namespace App\Http\Resources\Model\Construct;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelConstructSpecification extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code_1c'                        => $this->code_1c,
            'name'                           => $this->name,

            'constructs'                     => ModelConstructResource::collection($this->whenLoaded('constructs')),

            //'serial'                         => $this->serial,
            //'model_manufacture_status_id'    => $this->model_manufacture_status_id,
            //'model_collection_code_1c'       => $this->model_collection_code_1c,
            //'model_type_code_1c'             => $this->model_type_code_1c,
            //'name_short'                     => $this->name_short,
            //'name_common'                    => $this->name_common,
            //'name_report'                    => $this->name_report,
            //'cover_code_1c_copy'             => $this->cover_code_1c_copy,
            //'cover_name_1c'                  => $this->cover_name_1c,
            //'base_height'                    => $this->base_height,
            //'cover_height'                   => $this->cover_height,
            //'textile'                        => $this->textile,
            //'textile_composition'            => $this->textile_composition,
            //'cover_type'                     => $this->cover_type,
            //'zipper'                         => $this->zipper,
            //'spacer'                         => $this->spacer,
            //'stitch_pattern'                 => $this->stitch_pattern,
            //'pack_type'                      => $this->pack_type,
            //'base_composition'               => $this->base_composition,
            //'side_foam'                      => $this->side_foam,
            //'base_block'                     => $this->base_block,
            //'load'                           => $this->load,
            //'guarantee'                      => $this->guarantee,
            //'life'                           => $this->life,
            //'cover_mark'                     => $this->cover_mark,
            //'model_mark'                     => $this->model_mark,
            //'model_manufacture_group_id'     => $this->model_manufacture_group_id,
            //'owner'                          => $this->owner,
            //'lamit'                          => $this->lamit,
            //'sewing_machine'                 => $this->sewing_machine,
            //'kant'                           => $this->kant,
            //'tkch'                           => $this->tkch,
            //'pack_density'                   => $this->pack_density,
            //'side_height'                    => $this->side_height,
            //'pack_weight_rb'                 => $this->pack_weight_rb,
            //'pack_weight_ex'                 => $this->pack_weight_ex,
            //'model_manufacture_type_code_1c' => $this->model_manufacture_type_code_1c,
            //'weight'                         => $this->weight,
            //'barcode'                        => $this->barcode,
            //'base'                           => $this->base,
            //'cover'                          => $this->cover,
            //'active'                         => $this->active,
            //'status'                         => $this->status,
            //'description'                    => $this->description,
            //'comment'                        => $this->comment,
            //'note'                           => $this->note,
            //'meta'                           => $this->meta,
            //'created_at'                     => $this->created_at,
            //'updated_at'                     => $this->updated_at,
            //'cover_code_1c'                  => $this->cover_code_1c,
            //'sewing_operation_schema_id'     => $this->sewing_operation_schema_id,

            //'_' => parent::toArray($request)
        ];
    }
}
