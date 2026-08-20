<?php

namespace App\Http\Resources\Manufacture\Cells\Assembly\Manage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssemblyTaskStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return [
            'sector'           => $this->sector,
            'finished_amount'  => $this->finished_amount,
            'total_amount'     => $this->total_amount,
            //'assembly_task_id' => $this->assembly_task_id,
            // '_'           => parent::toArray($request),
        ];
    }
}
