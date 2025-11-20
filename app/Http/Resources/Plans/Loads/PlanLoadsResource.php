<?php /** @noinspection ALL */

namespace App\Http\Resources\Plans\Loads;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanLoadsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'active' => $this->active,
            'amounts' => $this->amounts,
            'order_no' => $this->order_no,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'load_at' => Carbon::parse($this->load_at)->format('Y-m-d H:i:s'),
            'load_at_previous' => $this->unload_at ? Carbon::parse($this->load_at_previous)->format('Y-m-d H:i:s') : null,
            'unload_at' => $this->unload_at ? Carbon::parse($this->unload_at)->format('Y-m-d H:i:s') : null,
            'load_position' => $this->load_position ?? 0,
            'period' => $this->period,
            'status' => $this->status,
            'description' => $this->description,
            'client' => [
                'id' => $this->client->id,
                'active' => $this->client->active,
                'name' => $this->client->name,
                'add_name' => $this->client->add_name,
                'short_name' => $this->client->short_name,
                'region' =>  $this->client->region,
            ],
            'order_type' => [
                'id' => $this->orderType->id,
                'name' => $this->orderType->name,
                'display_name' => $this->orderType->display_name,
                'color' => $this->orderType->color,
            ],


            // '_' => parent::toArray($request),
        ];
    }
}
