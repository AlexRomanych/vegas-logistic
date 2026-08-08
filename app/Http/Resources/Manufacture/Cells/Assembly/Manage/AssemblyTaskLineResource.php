<?php
/** @noinspection ALL */

namespace App\Http\Resources\Manufacture\Cells\Assembly\Manage;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssemblyTaskLineResource extends JsonResource
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
            'false_at'       => $this->false_at ? Carbon::parse($this->false_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_at'    => $this->finished_at ? Carbon::parse($this->finished_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'false_reason'   => $this->false_reason,
            'time'           => $this->time,
            'productivity'   => $this->square,
            'description'    => $this->description,
            'finished_by'    => $this->finished_by,

            'sector_lines' => $this->whenLoaded('sectors', fn() => AssemblyTaskLineSectorResource::collection($this->sectors)),

            'order_line' => (new AssemblyTaskOrderLineResource($this->whenLoaded('orderLine'))),

            //'order_line' => (new AssemblyTaskOrderLineResource($this->whenLoaded('orderLine')))
            //    ->additional([
            //        'phantom_data' => [
            //            'phantom'      => $this->phantom,
            //            'phantom_json' => $this->phantom_json,
            //        ]
            //    ]),        // __ Добавляем подмену свойств в потомке

            // 'updated_at'     => $this->updated_at,
            // 'color'          => $this->color,
            // 'meta'           => $this->meta,
            // 'note'           => $this->note,
            // 'comment'        => $this->comment,
            // 'status'         => $this->status,
            // 'active'         => $this->active,
            // 'cutting_task_id' => $this->cutting_task_id,
            //'block'          => $this->whenLoaded('block', fn() => new BlockTaskLineBlockResource($this->block)),
            //'order_lines'    => BlockTaskOrderLineResource::collection($this->whenLoaded('orderLines')),

            //'_' => parent::toArray($request),

        ];
    }


}
