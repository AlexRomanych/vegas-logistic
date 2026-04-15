<?php
/** @noinspection ALL */

namespace App\Http\Resources\Model\Show;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code_1c' => $this->code_1c,

            'serial'      => $this->serial,
            'name'        => $this->name,
            'name_report' => $this->name_report,
            //'name_short'  => $this->name_short,
            //'name_common' => $this->name_common,

            'base_height'         => $this->base_height,
            'cover_height'        => $this->cover_height,
            'textile'             => $this->textile,
            'textile_composition' => $this->textile_composition,
            'stitch_pattern'      => $this->stitch_pattern,
            'base_composition'    => $this->base_composition,
            'side_foam'           => $this->side_foam,
            'side_height'         => $this->side_height,
            'base_block'          => $this->base_block,
            'lamit'               => $this->lamit,
            'sewing_machine'      => $this->sewing_machine,
            'kant'                => $this->kant,
            'tkch'                => $this->tkch,
            'kdch'                => $this->kdch,
            'cover_type'          => $this->cover_type,
            'barcode'             => $this->barcode,
            'description'         => $this->description,

            // __ Чехол
            'cover'               => $this->relationLoaded('cover') ? new self($this->getRelation('cover')) : null,
            //'cover_name_1c'      => $this->cover_name_1c,
            //'cover_code_1c'      => $this->cover_code_1c,
            //'cover_code_1c_copy' => $this->cover_code_1c_copy,

            // __ Группа производства (FMX, Неопознанные, и т.д.)
            'manufacture_group'   => new ModelShowManufactureGroupResource($this->whenLoaded('manufactureGroup')),
            //'model_manufacture_group_id' => $this->model_manufacture_group_id,

            // __ Тип производства (Производство матрасов, Производство чехлов, Производство подушек, и т.д.)
            'manufacture_type'    => new ModelShowManufactureTypeResource($this->whenLoaded('manufactureType')),
            //'model_manufacture_type_code_1c' => $this->model_manufacture_type_code_1c,

            // __ Схема трудозатрат по швейным операциям
            'sewing_schema'       => new ModelShowOperationSchemaResource($this->whenLoaded('sewingSchema')),
            //'sewing_operation_schema_id' => $this->sewing_operation_schema_id,

            // __ Статус производства (Выпускается, Вариант исполнения, Архив, и т.д.)
            'manufacture_status'  => new ModelShowManufacturStatusResource($this->whenLoaded('manufactureStatus')),
            //'model_manufacture_status_id' => $this->model_manufacture_status_id,

            // __ Коллекция
            'collection'          => new ModelShowCollectionResource($this->whenLoaded('collection')), // 'collection
            //'model_collection_code_1c' => $this->model_collection_code_1c,

            // __ Тип изделия (матрас, чехол, подушка и т.д.)
            'model_type'          => new ModelShowModelTypeResource($this->whenLoaded('modelType')),
            //'model_type_code_1c'          => $this->model_type_code_1c,


            //'zipper'         => $this->zipper,
            //'spacer'         => $this->spacer,
            //'pack_type'      => $this->pack_type,
            //'load'           => $this->load,
            //'guarantee'      => $this->guarantee,
            //'life'           => $this->life,
            //'cover_mark'     => $this->cover_mark,
            //'model_mark'     => $this->model_mark,
            //'owner'          => $this->owner,
            //'pack_density'   => $this->pack_density,
            //'pack_weight_rb' => $this->pack_weight_rb,
            //'pack_weight_ex' => $this->pack_weight_ex,
            //'weight'         => $this->weight,
            //'active'         => $this->active,
            //'status'         => $this->status,
            //'comment'        => $this->comment,
            //'note'           => $this->note,
            //'meta'           => $this->meta,
            //'base'           => $this->base,
            //'cover'          => $this->cover,

            //'updated_at'                     => $this->updated_at,
            //'created_at'                     => $this->created_at,

            //'_' => parent::toArray($request)
        ];
    }
}
