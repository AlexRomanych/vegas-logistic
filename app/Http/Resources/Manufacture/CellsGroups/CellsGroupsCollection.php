<?php

namespace App\Http\Resources\Manufacture\CellsGroups;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CellsGroupsCollection extends ResourceCollection
{
    public static $wrap = 'cells_groups';

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
