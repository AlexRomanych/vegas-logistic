<?php

namespace App\Http\Resources\Manufacture\Cells\Sewing;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SewingTaskOrderResource extends JsonResource
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
            'id'              => $this->id,
            'order_no_num'    => $this->order_no_num,
            'order_no_str'    => $this->order_no_str,
            'order_no_origin' => $this->order_no_origin,
            'load_at'         => Carbon::parse($this->load_at)->format(RETURN_DATE_TIME_FORMAT),
            'comment_1c'      => $this->comment_1c,
            'client'          => new SewingTaskOrderClientResource($this->client),

            // 'order_period'       => $this->order_period,
            // 'elements_type_ref'  => $this->elements_type_ref,
            // 'elements_type'      => $this->elements_type,
            // 'status'             => $this->status,
            // 'status_history'     => $this->status_history,
            // 'amounts'            => $this->amounts,
            // 'is_forecast'        => $this->is_forecast,
            // 'shown'              => $this->shown,
            // 'stat_include'       => $this->stat_include,
            // 'load_position'      => $this->load_position,
            // 'responsible'        => $this->responsible,
            // 'manager_load_date'  => $this->manager_load_date,
            // 'manager_start'      => $this->manager_start,
            // 'manager_end'        => $this->manager_end,
            // 'design_start'       => $this->design_start,
            // 'design_end'         => $this->design_end,
            // 'no_1c'              => $this->no_1c,
            // 'code_1c'            => $this->code_1c,
            // 'base_order_code_1c' => $this->base_order_code_1,
            // 'client_code_1c'     => $this->client_code_1c,
            // 'client_name_1c'     => $this->client_name_1c,
            // 'active'             => $this->active,
            // 'active_logistic'    => $this->active_logistic,
            // 'service'            => $this->service,
            // 'service_ext'        => $this->service_ext,
            // 'extended_meta'      => $this->extended_meta,
            // 'description'        => $this->description,
            // 'comment'            => $this->comment,
            // 'note'               => $this->note,
            // 'meta'               => $this->meta,
            // 'history'            => $this->history,
            // 'meta_ext'           => $this->meta_ext,
            // 'created_at'         => $this->created_at,
            // 'updated_at'         => $this->updated_at,
            // 'client_id'              => $this->client_id,
            // 'order_type_id'          => $this->order_type_id,
            // 'plan_load_id'           => $this->plan_load_id,

            // 'load_at_previous'       => $this->load_at_previous,
            // 'unload_at'              => $this->unload_at,
            // 'load_at_dates_conflict' => $this->load_at_dates_conflict,
            // 'storage_id'             => $this->storage_id,

            // '_' => parent::toArray($request),
        ];
    }
}
