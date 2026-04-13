<?php /** @noinspection ALL */

namespace App\Http\Resources\Model\Construct;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelConstructProcedureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'           => $this->name,
            //'object_name'    => $this->object_name,
            //'code_1c'        => $this->code_1c,
            //'text'           => $this->text,
            //'object_code_1c' => $this->object_code_1c,
            //'active'         => $this->active,
            //'status'         => $this->status,
            //'description'    => $this->description,
            //'comment'        => $this->comment,
            //'note'           => $this->note,
            //'meta'           => $this->meta,
            //'color'          => $this->color,
            //'created_at'     => $this->created_at,
            //'updated_at'     => $this->updated_at,
            //'text_vba'       => $this->text_vba,
            //'text_parser'    => $this->text_parser,
            //'text_reserved'  => $this->text_reserved,

            //'_' => parent::toArray($request)
        ];
    }
}
