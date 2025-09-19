<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricPictureTuningTimePicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @noinspection PhpUndefinedFieldInspection
     */
    public function toArray(Request $request): array
    {
        return [
            'pic' => [
                'id' => $this->picTo->id,
                'name' => $this->picTo->name,
                'active' => $this->picTo->active,
                'machine' => [
                    'id' => $this->picTo->fabricMainMachine->id,
                    'name' => $this->picTo->fabricMainMachine->name,
                    'short_name' => $this->picTo->fabricMainMachine->short_name,
                ],
            ],
            'tuning_time' => $this->tuning_time,
        ];

        // return [
        //     'pic_from' => [
        //         'id' => $this->picFrom->id,
        //         'name' => $this->picFrom->name,
        //         'active' => $this->picFrom->active,
        //         'machine' => [
        //             'id' => $this->picFrom->fabricMainMachine->id,
        //             'name' => $this->picFrom->fabricMainMachine->name,
        //             'short_name' => $this->picFrom->fabricMainMachine->short_name,
        //         ],
        //     ],
        //     'pic_to' => [
        //         'id' => $this->picTo->id,
        //         'name' => $this->picTo->name,
        //         'active' => $this->picTo->active,
        //         'machine' => [
        //             'id' => $this->picTo->fabricMainMachine->id,
        //             'name' => $this->picTo->fabricMainMachine->name,
        //             'short_name' => $this->picTo->fabricMainMachine->short_name,
        //         ],
        //     ],
        //     'tuning_time' => $this->tuning_time,
        // ];

        // return parent::toArray($request);
    }
}
