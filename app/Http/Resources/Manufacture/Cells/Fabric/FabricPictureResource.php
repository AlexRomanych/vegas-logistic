<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricPictureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'stitch_length' => $this->stitch_length,
            'stitch_speed' => $this->stitch_speed,
            'moment_speed' => $this->moment_speed,
            'shuttle_amount' => $this->shuttle_amount,
            'description' => $this->description ?? '',
            'productivity' => $this->productivity,
            'machines' =>
                [
                    'fabricMainMachine' =>
                        [
                            'machine' =>
                                [
                                    'id' => $this->fabricMainMachine->id,
                                    'active' => $this->fabricMainMachine->active,
                                    'name' => $this->fabricMainMachine->name,
                                    'short_name' => $this->fabricMainMachine->short_name,
                                    'description' => $this->fabricMainMachine->description ?? '',
                                ],
                            'schema' =>
                                [
                                    'id' => $this->fabricMainMachineSchema->id,
                                    'schema_name' => $this->fabricMainMachineSchema->schema_name,
                                    'schema' => $this->fabricMainMachineSchema->schema,
                                    'description' => $this->fabricMainMachineSchema->description ?? '',
                                ]
                        ],


                    'fabricAltMachine_1' =>
                        [
                            'machine' =>
                                [
                                    'id' => $this->fabricAltMachine_1->id,
                                    'active' => $this->fabricAltMachine_1->active,
                                    'name' => $this->fabricAltMachine_1->name,
                                    'short_name' => $this->fabricAltMachine_1->short_name,
                                    'description' => $this->fabricAltMachine_1->description ?? '',
                                ],
                            'schema' =>
                                [
                                    'id' => $this->fabricAltMachineSchema_1->id,
                                    'schema_name' => $this->fabricAltMachineSchema_1->schema_name,
                                    'schema' => $this->fabricAltMachineSchema_1->schema,
                                    'description' => $this->fabricAltMachineSchema_1->description ?? '',
                                ]
                        ],

                    'fabricAltMachine_2' =>
                        [
                            'machine' =>
                                [
                                    'id' => $this->fabricAltMachine_2->id,
                                    'active' => $this->fabricAltMachine_2->active,
                                    'name' => $this->fabricAltMachine_2->name,
                                    'short_name' => $this->fabricAltMachine_2->short_name,
                                    'description' => $this->fabricAltMachine_2->description ?? '',
                                ],
                            'schema' =>
                                [
                                    'id' => $this->fabricAltMachineSchema_2->id,
                                    'schema_name' => $this->fabricAltMachineSchema_2->schema_name,
                                    'schema' => $this->fabricAltMachineSchema_2->schema,
                                    'description' => $this->fabricAltMachineSchema_2->description ?? '',
                                ]
                        ],

                    'fabricAltMachine_3' =>
                        [
                            'machine' =>
                                [
                                    'id' => $this->fabricAltMachine_3->id,
                                    'active' => $this->fabricAltMachine_3->active,
                                    'name' => $this->fabricAltMachine_3->name,
                                    'short_name' => $this->fabricAltMachine_3->short_name,
                                    'description' => $this->fabricAltMachine_3->description ?? '',
                                ],
                            'schema' =>
                                [
                                    'id' => $this->fabricAltMachineSchema_3->id,
                                    'schema_name' => $this->fabricAltMachineSchema_3->schema_name,
                                    'schema' => $this->fabricAltMachineSchema_3->schema,
                                    'description' => $this->fabricAltMachineSchema_3->description ?? '',
                                ],
                        ],
                ],
        ];

//        return parent::toArray($request);
    }
}
