<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\BusinessProcess;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessNodeForAdjacencyListResource extends JsonResource
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

            // 'active'        => $this->active,
            // 'allow_action'  => $this->allow_action,
            // 'description'   => $this->description,
            // 'has_module'    => $this->has_module,
            // 'meta'          => $this->meta,
            // 'meta_extended' => $this->meta_extended,
            // 'node_start'    => $this->node_start,
            // 'note'          => $this->note,
            // 'order'         => $this->order,
            // 'pivot'         => $this->pivot,
            // 'route_name'    => $this->route_name,
            // 'status'        => $this->status,
            // 'type'          => $this->type,

        ];

    }
}
