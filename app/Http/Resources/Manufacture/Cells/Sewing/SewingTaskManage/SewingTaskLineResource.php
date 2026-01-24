<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage;

use App\Classes\SewingTimeLabor;
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
        // __ Получаем объект с трудозатратами
        /** @noinspection PhpUndefinedFieldInspection */
        $labor = new SewingTimeLabor(
            model: $this->orderLine->model,
            size: $this->orderLine->size,
            amount: $this->amount
        );


        /** @noinspection PhpUndefinedFieldInspection */
        return [
            'id'           => $this->id,
            'amount'       => $this->amount,
            'position'     => $this->position,
            'created_at'   => $this->created_at ? Carbon::parse($this->created_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_at'  => $this->finished_at ? Carbon::parse($this->finished_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_by'  => $this->finished_by ? Carbon::parse($this->finished_by)->format(RETURN_DATE_TIME_FORMAT) : null,
            'false_reason' => $this->false_reason,

            'amount_avg' => null,
            'time'       => ['time_' . $this->phantom => $this->time],

            // 'amount_avg' => $this->orderLine->model->is_average ? $labor->getAveragesAmount() : null,
            // 'time'       => $labor->getTime(),

            'order_line' => (new SewingTaskOrderLineResource($this->whenLoaded('orderLine')))
                ->additional([
                    'phantom_data' => [
                        'phantom'      => $this->phantom,
                        'phantom_json' => $this->phantom_json,
                    ]
                ]),        // __ Добавляем подмену свойств в потомке

            'is_average' => $this->orderLine->model->is_average,    // __ Динамическое поле, указывает, что модель расчетная

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
