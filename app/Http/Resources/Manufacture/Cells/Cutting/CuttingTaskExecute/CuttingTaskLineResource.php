<?php

namespace App\Http\Resources\Manufacture\Cells\Cutting\CuttingTaskExecute;

use App\Classes\CuttingTimeLabor;
use App\Http\Resources\Manufacture\Cells\Cutting\CuttingTaskManage\CuttingTaskOrderLineResource;
use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Services\ModelsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuttingTaskLineResource extends JsonResource
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
            // 'cutting_task_id' => $this->cutting_task_id,
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
