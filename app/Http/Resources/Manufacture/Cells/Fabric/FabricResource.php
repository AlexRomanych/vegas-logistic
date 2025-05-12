<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

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

            'picture' => [
                'id' => $this->fabric_picture_id,
                'name' => $this->pic,                               // вычисляемое поле
            ],

            'textile' => $this->textile,                            // вычисляемое поле
            'fillersList' => $this->fillersList,                    // вычисляемое поле
            'active' => $this->active,
            'rare' => $this->rare,
            'correct' => $this->fabricCorrect,

            'machines' => [
                [
                    'id' => $this->fabricPicture->fabricMainMachine->id,                    // Тут дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                    'short_name' => $this->fabricPicture->fabricMainMachine->short_name,    // Тут дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                ],
                [
                    'id' => $this->fabricPicture->fabricAltMachine_1->id,                    // Тут дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                    'short_name' => $this->fabricPicture->fabricAltMachine_1->short_name,    // Тут дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                ],
                [
                    'id' => $this->fabricPicture->fabricAltMachine_2->id,                    // Тут дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                    'short_name' => $this->fabricPicture->fabricAltMachine_2->short_name,    // Тут дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                ],
                [
                    'id' => $this->fabricPicture->fabricAltMachine_3->id,                    // Тут дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                    'short_name' => $this->fabricPicture->fabricAltMachine_3->short_name,    // Тут дергаем стегальную машину через цепочку отношений ПС->Рисунок ПС->Стегальная машина (Fabric->FabricPicture->FabricMachine)
                ],
            ],

            'buffer' => [
                'amount' => (double)$this->buffer_amount,
                'min' => (double)$this->buffer_min,
                'max' => (double)$this->buffer_max,
                'min_rolls' => (double)$this->buffer_min_rolls,
                'max_rolls' => (double)$this->buffer_max_rolls,
                'optimal_party' => (double)$this->optimal_party,
                'average_length' => (double)$this->average_roll_length,
                'rate' => (double)$this->translate_rate,
                'productivity' => (double)$this->productivity,

            ],

            'text' => [
                'description' => $this->description,
                'comment' => $this->comment,
                'note' => $this->note,
            ],

        ];
    }
}
