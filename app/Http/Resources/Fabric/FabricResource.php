<?php

namespace App\Http\Resources\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricResource extends JsonResource
{
    public static $wrap = 'fabric';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'code_1C' => $this->code_1C,
            'name' => $this->name,
            'display_name' => $this->display_name,                  // вычисляемое поле
            'pic' => $this->pic,                                    // вычисляемое поле
            'textile' => $this->textile,                            // вычисляемое поле
            'fillersList' => $this->fillersList,                    // вычисляемое поле
            'active' => $this->active,
            'fabric_picture_id' => $this->fabric_picture_id,
            'fabric_machine' => [
                'id' => $this->fabricPicture->fabricMainMachine->id,                    // Туту дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                'short_name' => $this->fabricPicture->fabricMainMachine->short_name,    // Туту дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
            ],
            'buffer' => (double)$this->buffer_amount,
            'optimal_party' => (double)$this->optimal_party,
            'description' => $this->description,
        ];
    }
}
