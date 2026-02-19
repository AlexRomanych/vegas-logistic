<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskExecute;

use App\Classes\SewingTimeLabor;
use App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage\SewingTaskOrderLineResource;
use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Services\ModelsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingTaskLineResource extends JsonResource
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
            'id'           => $this->id,
            'amount'       => $this->amount,
            'position'     => $this->position,
            'created_at'   => $this->created_at ? Carbon::parse($this->created_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_at'  => $this->finished_at ? Carbon::parse($this->finished_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_by'  => $this->finished_by,
            'false_at'     => $this->false_at ? Carbon::parse($this->false_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'false_reason' => $this->false_reason,

            // 'order_line_id' => $this->order_line_id,
            // 'sewing_task_id' => $this->sewing_task_id,
            // 'description'    => $this->description,
            // 'active'         => $this->active,
            // 'status'         => $this->status,
            // 'comment'        => $this->comment,
            // 'note'           => $this->note,
            // 'meta'           => $this->meta,
            // 'color'          => $this->color,
            // 'updated_at'     => $this->updated_at,

            // '_' => parent::toArray($request),
        ];
    }
}
