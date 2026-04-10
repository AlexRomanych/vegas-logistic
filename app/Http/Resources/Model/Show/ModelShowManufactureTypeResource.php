<?php /** @noinspection ALL */

namespace App\Http\Resources\Model\Show;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelShowManufactureTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code_1c' => $this->code_1c,
            'name'    => $this->name,

            //'_' => parent::toArray($request)
        ];
    }
}
