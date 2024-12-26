<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CellSewingUniversalCollection extends ResourceCollection
{
    public static $wrap = 'sewing';

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
