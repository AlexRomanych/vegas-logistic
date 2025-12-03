<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\BusinessProcess;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessProcessNodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // '_'            => parent::toArray($request),
            'id'           => $this->id,
            'name'         => $this->name,
            'type'         => $this->type,
            'has_module'   => $this->has_module,
            'route_name'   => $this->route_name,
            'allow_action' => $this->allow_action,
            'active'       => $this->active,
            'status'       => $this->status,
            'order'        => $this->order,
            'description'  => $this->description,
        ];
    }
}
