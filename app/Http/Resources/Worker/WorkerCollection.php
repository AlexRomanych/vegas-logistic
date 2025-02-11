<?php

namespace App\Http\Resources\Worker;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WorkerCollection extends ResourceCollection
{

    public static $wrap = 'workers';
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
