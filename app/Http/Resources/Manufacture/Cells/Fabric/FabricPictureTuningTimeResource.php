<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricPictureTuningTimeResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'machine' => [
                'id' => $this->fabricMainMachine->id,
                'name' => $this->fabricMainMachine->name,
                'short_name' => $this->fabricMainMachine->short_name,
            ],

            'pictures' => FabricPictureTuningTimePicResource::collection($this->picturesFrom),

            // 'pictures_to' => FabricPictureTuningTimePicResource::collection($this->picturesTo),
            // 'pictures_from' => FabricPictureTuningTimePicResource::collection($this->picturesFrom),

            // 'pictures_to' => $this->picturesFrom,
            // 'pictures_to' => $this->picturesTo,
        ];

        // return parent::toArray($request);
    }
}
