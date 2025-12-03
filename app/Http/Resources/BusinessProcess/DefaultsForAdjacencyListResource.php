<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\BusinessProcess;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefaultsForAdjacencyListResource extends JsonResource
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

            'client' => [
                'id' => $this->id,
                'name' => $this->name,
                'short_name' => $this->short_name,
            ],
            'offset' => $this->pivot->offset,
            'process_node_ref_id'=> $this->pivot->process_node_ref_id,

        ];

    }
}
