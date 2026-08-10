<?php
/** @noinspection ALL */

namespace App\Http\Resources\Manufacture\Cells\Assembly\Manage;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssemblyTaskLineSectorResource extends JsonResource
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
            'id'          => $this->id,
            'dims'        => [
                'width'  => $this->width,
                'length' => $this->length,
                'height' => $this->height,
            ],
            'detail_dims' => [
                'width'  => $this->detail_width,
                'length' => $this->detail_length,
                'height' => $this->detail_height,
            ],

            'sector' => $this->sector,

            'material_code_1c' => $this->material_code_1c,
            'material_name'    => $this->material_name,

            'expense' => $this->expense,
            'rest'    => $this->rest,
            'total'   => $this->total,

            'expense_per_pic' => $this->expense_per_pic,
            'rest_per_pic'    => $this->rest_per_pic,
            'total_per_pic'   => $this->total_per_pic,

            'amount' => $this->amount,
            'count'  => $this->count,
            'time'   => $this->time,

            'description' => $this->description,

            'finished_at'   => $this->finished_at,
            'false_at'      => $this->false_at,
            'finished_by'   => $this->finished_by,
            'false_reason'  => $this->false_reason,
            'false_history' => $this->false_history,

            //'comment'               => $this->comment,
            //'note'                  => $this->note,
            //'meta'                  => $this->meta,
            //'color'                 => $this->color,

            //'created_at'            => $this->created_at,
            //'updated_at'            => $this->updated_at,

            //'time_json'             => $this->time_json,
            //'outputs'               => $this->outputs,
            //'active'                => $this->active,
            //'status'                => $this->status,

            //'assembly_task_line_id' => $this->assembly_task_line_id,
            //'order_line_id'         => $this->order_line_id,

            //'assembly_task_line_id' => $this->assembly_task_line_id,
            //'order_line_id'         => $this->order_line_id,

            //'detail_width'          => $this->detail_width,
            //'detail_length'         => $this->detail_length,
            //'detail_height'         => $this->detail_height,

            //'_' => parent::toArray($request),
        ];
    }


}
