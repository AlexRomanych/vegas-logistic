<?php

namespace App\Http\Resources\Manufacture\CellsGroups;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CellsGroupsResource extends JsonResource
{
    public static $wrap = 'cells_groups';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
