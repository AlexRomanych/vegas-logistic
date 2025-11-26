<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\BusinessProcess;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessProcessForListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // '__' => parent::toArray($request),

            'id'             => $this->id,
            'name'           => $this->name,
            'active'         => $this->active,
            'description'    => $this->description,
            'comment'        => $this->comment,
            'belongs_to'     => $this->belongs_to,
            'type'           => $this->type,
            'start_node'     => $this->whenLoaded('startNode', function () {
                return new BusinessProcessNodeForListResource($this->startNode);
            }),
            'finish_node'    => $this->whenLoaded('finishNode', function () {
                return new BusinessProcessNodeForListResource($this->finishNode);
            }),
            'reference_node' => $this->whenLoaded('referenceNode', function () {
                return new BusinessProcessNodeForListResource($this->referenceNode);
            }),
            // 'reference_node'    => $this->whenLoaded('referenceNode', new BusinessProcessNodeResourceForList($this->referenceNode)),
            // 'reference_node'    => $this->reference_node,


            // 'note'              => $this->note,
            // 'status'            => $this->status,
            // 'meta'              => $this->meta,
            // 'meta_extended'     => $this->meta_extended,
            // 'start_node_id'     => $this->start_node_id,
            // 'finish_node_id'    => $this->finish_node_id,
            // 'reference_node_id' => $this->reference_node_id,

        ];
    }
}
