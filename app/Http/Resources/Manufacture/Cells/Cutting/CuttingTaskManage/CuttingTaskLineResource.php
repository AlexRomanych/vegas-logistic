<?php

namespace App\Http\Resources\Manufacture\Cells\Cutting\CuttingTaskManage;

use App\Classes\CuttingTimeLabor;
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
        // __ Получаем объект с трудозатратами по самой записи
        /** @noinspection PhpUndefinedFieldInspection */
        $labor = new CuttingTimeLabor($this->resource);

        /** @noinspection PhpUndefinedFieldInspection */
        return [
            'id'           => $this->id,
            'id_ref'       => $this->id,
            'amount'       => $this->amount,
            'position'     => $this->position,
            'created_at'   => $this->created_at ? Carbon::parse($this->created_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_at'  => $this->finished_at ? Carbon::parse($this->finished_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'finished_by'  => $this->finished_by,
            'false_at'     => $this->false_at ? Carbon::parse($this->false_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'false_reason' => $this->false_reason,
            'table'        => $this->phantom,

            //'amount_avg' => null,
            'time'         => $labor->getRealTime(),
            //'time'       => $this->orderLine->model->is_average ? $labor->getTimeByPhantom($this->phantom) : $labor->getTimeArray(),
            // 'time'       => ['time_'.$this->phantom => $this->time],
            // 'time'       => $labor->getTime(),

            // 'amount_avg' => $this->orderLine->model->is_average ? $labor->getAveragesAmount() : null,

            'order_line' => (new CuttingTaskOrderLineResource($this->whenLoaded('orderLine')))
                ->additional([
                    'phantom_data' => [
                        'phantom'      => $this->phantom,
                        'phantom_json' => $this->phantom_json,
                    ]
                ]),        // __ Добавляем подмену свойств в потомке

            'element_type' => [
                'is_average' => $this->orderLine->model->is_average,    // __ Динамическое поле, указывает, что модель расчетная
                'is_base'    => ModelsService::isElementBase($this->orderLine->model->code_1c),
                'is_cover'   => ModelsService::isElementCover($this->orderLine->model->code_1c),
                'type'       =>
                    match (true) {
                        $this->orderLine->model->is_average                             => 'average',
                        ModelsService::isElementBase($this->orderLine->model->code_1c)  => 'base',
                        ModelsService::isElementCover($this->orderLine->model->code_1c) => 'cover',
                        default                                                         => 'unknown',
                    },
            ],


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
