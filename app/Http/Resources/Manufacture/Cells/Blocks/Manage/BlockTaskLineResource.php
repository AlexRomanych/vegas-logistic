<?php
/** @noinspection ALL */

namespace App\Http\Resources\Manufacture\Cells\Blocks\Manage;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockTaskLineResource extends JsonResource
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
            'id'             => $this->id,
            'id_ref'         => $this->id,
            'amount'         => $this->amount,
            'position'       => $this->position,
            'created_at'     => $this->created_at ? Carbon::parse($this->created_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_at'    => $this->finished_at ? Carbon::parse($this->finished_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_by'    => $this->finished_by,
            'false_at'       => $this->false_at ? Carbon::parse($this->false_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'false_reason'   => $this->false_reason,
            'manuf_line'     => $this->line,
            'time'           => $this->time,
            'square'         => $this->square,
            'productivity'   => $this->square,
            'order_line_ids' => $this->order_line_ids,
            'description'    => $this->description,

            'order_lines'    => BlockTaskOrderLineResource::collection($this->whenLoaded('orderLines')),
            'block'          => $this->whenLoaded('block', fn() => new BlockTaskLineBlockResource($this->block)),

            // 'cutting_task_id' => $this->cutting_task_id,
            // 'active'         => $this->active,
            // 'status'         => $this->status,
            // 'comment'        => $this->comment,
            // 'note'           => $this->note,
            // 'meta'           => $this->meta,
            // 'color'          => $this->color,
            // 'updated_at'     => $this->updated_at,

            //'_' => parent::toArray($request),
        ];
    }


}
