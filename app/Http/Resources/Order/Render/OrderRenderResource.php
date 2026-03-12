<?php

namespace App\Http\Resources\Order\Render;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @noinspection PhpUndefinedFieldInspection
     */
    public function toArray(Request $request): array
    {

        return [
            'id'                   => $this->id,
            'order_no_str'         => $this->order_no_str,
            'order_period'         => $this->order_period,
            'elements_type'        => $this->elements_type,
            'elements_type_ref'    => $this->elements_type_ref,
            'elements_type_render' => $this->elements_type_render,      // elements_type на русском языке
            'status'               => $this->status,
            'amounts'              => $this->amounts,
            'is_forecast'          => $this->is_forecast,
            'shown'                => $this->shown,
            'stat_include'         => $this->stat_include,
            'responsible'          => $this->responsible,
            'manager_load_date'    => $this->manager_load_date ? Carbon::parse($this->manager_load_date)->format(RETURN_DATE_TIME_FORMAT) : null,
            'manager_start'        => $this->manager_start ? Carbon::parse($this->manager_start)->format(RETURN_DATE_TIME_FORMAT) : null,
            'manager_end'          => $this->manager_end ? Carbon::parse($this->manager_end)->format(RETURN_DATE_TIME_FORMAT) : null,
            'design_start'         => $this->design_start ? Carbon::parse($this->design_start)->format(RETURN_DATE_TIME_FORMAT) : null,
            'design_end'           => $this->design_end ? Carbon::parse($this->design_end)->format(RETURN_DATE_TIME_FORMAT) : null,
            'comment_1c'           => $this->comment_1c,
            'client_name_1c'       => $this->client_name_1c,
            'active'               => $this->active,
            'load_at'              => $this->load_at ? Carbon::parse($this->load_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'unload_at'            => $this->unload_at ? Carbon::parse($this->unload_at)->format(RETURN_DATE_TIME_FORMAT) : null,
            'no_1c'                => $this->no_1c,
            'code_1c'              => $this->code_1c,
            'description'          => $this->description,
            'created_at'           => $this->created_at ? Carbon::parse($this->created_at)->format(RETURN_DATE_TIME_FORMAT) : null,

            'client' => [
                'id'         => $this->client->id,
                'short_name' => $this->client->short_name
            ],
            // 'client_id'     => $this->client_id,

            'order_type' => [
                // 'id'           => $this->orderType->id,
                'color'        => $this->orderType->color,
                'display_name' => $this->orderType->display_name,
            ],
            // 'order_type_id' => $this->order_type_id,

            'lines' => OrderLineRenderResource::collection($this->lines),


            // 'updated_at'             => $this->updated_at,
            // 'load_at_dates_conflict' => $this->load_at_dates_conflict,
            // 'storage_id'             => $this->storage_id,

            // '_
            //' => parent::toArray($request)
            // 'load_position'          => $this->load_position,
            // 'order_no_origin'        => $this->order_no_origin,
            // 'order_no_num'           => $this->order_no_num,
            // 'plan_load_id'           => $this->plan_load_id,
            // 'load_at_previous'       => $this->load_at_previous,
            // 'extended_meta'          => $this->extended_meta,
            // 'service_ext'            => $this->service_ext,
            // 'service'                => $this->service,
            // 'client_code_1c'         => $this->client_code_1c,
            // 'base_order_code_1c'     => $this->base_order_code_1c,
            // 'meta_ext'               => $this->meta_ext,
            // 'history'                => $this->history,
            // 'meta'                   => $this->meta,
            // 'note'                   => $this->note,
            // 'comment'                => $this->comment,
        ];

    }
}
