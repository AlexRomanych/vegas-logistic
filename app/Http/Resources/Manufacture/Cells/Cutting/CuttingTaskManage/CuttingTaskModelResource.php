<?php

namespace App\Http\Resources\Manufacture\Cells\Cutting\CuttingTaskManage;

use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuttingTaskModelResource extends JsonResource
{

    private function isPhantomMachineTypeSet(): bool
    {
        return isset($this->additional['phantom_data']['phantom']);
    }

    // __ Возвращаем тип машины по подмене свойств
    private function getPhantomMachineType()
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'];
        } else {
            return $this->machine_type_name;
        }
    }


    // __ Возвращаем логическое свойство принадлежности к машине
    private function setPhantomMachineTypeUniversal(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === CuttingTask::FIELD_UNIVERSAL;
        } else {
            return $this->is_universal;
        }
    }

    private function setPhantomMachineTypeAuto(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === CuttingTask::FIELD_AUTO;
        } else {
            return $this->is_auto;
        }
    }

    private function setPhantomMachineTypeSolidHard(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === CuttingTask::FIELD_SOLID_HARD;
        } else {
            return $this->is_solid_hard;
        }
    }

    private function setPhantomMachineTypeSolidLite(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === CuttingTask::FIELD_SOLID_LITE;
        } else {
            return $this->is_solid_lite;
        }
    }

    private function setPhantomMachineTypeUndefined(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === CuttingTask::FIELD_UNDEFINED;
        } else {
            return $this->is_undefined;
        }
    }

    private function setPhantomMachineTypeAverage(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === CuttingTaskLine::FIELD_AVERAGE;
        } else {
            return $this->is_average;
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        /** @noinspection PhpUndefinedFieldInspection */
        return [
            'code_1c'             => $this->code_1c,
            'name'                => $this->name,
            'name_report'         => $this->name_report,
            'base_height'         => $this->base_height,
            'cover_height'        => $this->cover_height,
            'textile'             => $this->textile,
            'textile_composition' => $this->textile_composition,
            'cover_type'          => $this->cover_type,
            'base_composition'    => $this->base_composition,
            'kant'                => $this->kant,
            'tkch'                => $this->tkch,
            'kdch'                => $this->kdch,
            'kdch_doc'            => $this->kdchDoc?->id,
            //'kdch_doc' => $this->whenLoaded('kdchDoc', function ($kdchDoc) {
            //    return [
            //        'id'        => $kdchDoc?->id, // Полезно иметь ID на фронте для скачивания
            //        'file_path' => $kdchDoc?->file_path
            //    ];
            //}),
            'is_solid'            => $this->is_solid,
            'is_universal'        => $this->is_universal,
            'is_auto'             => $this->is_auto,
            'is_solid_hard'       => $this->is_solid_hard,
            'is_solid_lite'       => $this->is_solid_lite,
            'is_undefined'        => $this->is_undefined,
            'is_average'          => $this->is_average,
            'machine_type'        => $this->machine_type_name,
            'machine_type_ref'    => $this->machine_type,       // __ референсный тип машины (оригинальный)
            'machine_type_name'    => $this->machine_type_name,
            //'is_universal'        => $this->setPhantomMachineTypeUniversal(),
            //'is_auto'             => $this->setPhantomMachineTypeAuto(),
            //'is_solid_hard'       => $this->setPhantomMachineTypeSolidHard(),
            //'is_solid_lite'       => $this->setPhantomMachineTypeSolidLite(),
            //'is_undefined'        => $this->setPhantomMachineTypeUndefined(),
            //'is_average'          => $this->setPhantomMachineTypeAverage(),
            //'machine_type'        => $this->getPhantomMachineType(),


            // 'model_manufacture_status_id'    => $this->model_manufacture_status_id,
            // 'model_collection_code_1c'       => $this->model_collection_code_1c,
            // 'model_type_code_1c'             => $this->model_type_code_1c,
            // 'serial'                         => $this->serial,
            // 'name_short'                     => $this->name_short,
            // 'name_common'                    => $this->name_common,
            // 'cover_code_1c_copy'             => $this->cover_code_1c_copy,
            // 'cover_name_1c'                  => $this->cover_name_1c,
            // 'zipper'                         => $this->zipper,
            // 'spacer'                         => $this->spacer,
            // 'stitch_pattern'                 => $this->stitch_pattern,
            // 'pack_type'                      => $this->pack_type,
            // 'side_foam'                      => $this->side_foam,
            // 'base_block'                     => $this->base_block,
            // 'load'                           => $this->load,
            // 'guarantee'                      => $this->guarantee,
            // 'life'                           => $this->life,
            // 'cover_mark'                     => $this->cover_mark,
            // 'model_mark'                     => $this->model_mark,
            // 'model_manufacture_group_id'     => $this->model_manufacture_group_id,
            // 'owner'                          => $this->owner,
            // 'lamit'                          => $this->lamit,
            // 'cutting_machine'                 => $this->cutting_machine,
            // 'pack_density'                   => $this->pack_density,
            // 'side_height'                    => $this->side_height,
            // 'pack_weight_rb'                 => $this->pack_weight_rb,
            // 'pack_weight_ex'                 => $this->pack_weight_ex,
            // 'model_manufacture_type_code_1c' => $this->model_manufacture_type_code_1c,
            // 'weight'                         => $this->weight,
            // 'barcode'                        => $this->barcode,
            // 'base'                           => $this->base,
            // 'cover'                          => $this->cover,
            // 'active'                         => $this->active,
            // 'status'                         => $this->status,
            // 'description'                    => $this->description,
            // 'comment'                        => $this->comment,
            // 'note'                           => $this->note,
            // 'meta'                           => $this->meta,
            // 'created_at'                     => $this->created_at,
            // 'updated_at'                     => $this->updated_at,
            // 'cover_code_1c'                  => $this->cover_code_1c,

             '_' => parent::toArray($request),

        ];
    }
}
