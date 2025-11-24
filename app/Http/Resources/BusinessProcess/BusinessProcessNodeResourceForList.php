<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\BusinessProcess;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessProcessNodeResourceForList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // '__'            => parent::toArray($request),

            'id'            => $this->id,
            'name'          => $this->name,
            'active'        => $this->active,
            'comment'       => $this->comment,
            'description'   => $this->description,
            'route_name'    => $this->route_name,
            'has_module'    => $this->has_module,

            // 'type'          => $this->type,
            // 'meta'          => $this->meta,
            // 'meta_extended' => $this->meta_extended,
            // 'node_start'    => $this->node_start,
            // 'note'          => $this->note,
            // 'order'         => $this->order,
            // 'status'        => $this->status,
        ];
    }
}
