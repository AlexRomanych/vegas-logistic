<?php

namespace App\Http\Resources\Fabric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricMachineResource extends JsonResource
{
    public static $wrap = 'machine';
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
            'name' => $this->name,
            'short_name' => $this->short_name,
        ];
    }
}
