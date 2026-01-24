<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage;

use App\Models\Manufacture\Cells\Sewing\SewingTask;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingTaskModelResource extends JsonResource
{

    private function isPhantomMachineTypeSet(): bool
    {
        return !is_null($this->additional['phantom_data']['phantom']);
    }

    // __ Возвращаем тип машины по подмене свойств
    private function getPhantomMachineType()
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'];
        } else {
            return $this->machine_type;
        }
    }


    // __ Возвращаем логическое свойство принадлежности к машине
    private function setPhantomMachineTypeUniversal(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === SewingTask::FIELD_UNIVERSAL;
        } else {
            return $this->is_universal;
        }
    }

    private function setPhantomMachineTypeAuto(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === SewingTask::FIELD_AUTO;
        } else {
            return $this->is_auto;
        }
    }

    private function setPhantomMachineTypeSolidHard(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === SewingTask::FIELD_SOLID_HARD;
        } else {
            return $this->is_solid_hard;
        }
    }

    private function setPhantomMachineTypeSolidLite(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === SewingTask::FIELD_SOLID_LITE;
        } else {
            return $this->is_solid_lite;
        }
    }

    private function setPhantomMachineTypeUndefined(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === SewingTask::FIELD_UNDEFINED;
        } else {
            return $this->is_undefined;
        }
    }

    private function setPhantomMachineTypeAverage(): bool
    {
        if ($this->isPhantomMachineTypeSet()) {
            return $this->additional['phantom_data']['phantom'] === SewingTask::FIELD_AVERAGE;
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
            'is_solid'            => $this->is_solid,
            'is_universal'        => $this->setPhantomMachineTypeUniversal(),
            'is_auto'             => $this->setPhantomMachineTypeAuto(),
            'is_solid_hard'       => $this->setPhantomMachineTypeSolidHard(),
            'is_solid_lite'       => $this->setPhantomMachineTypeSolidLite(),
            'is_undefined'        => $this->setPhantomMachineTypeUndefined(),
            'is_average'          => $this->setPhantomMachineTypeAverage(),
            'machine_type'        => $this->getPhantomMachineType(),

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
            // 'sewing_machine'                 => $this->sewing_machine,
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

            // '_' => parent::toArray($request),

        ];
    }
}
