<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Manufacture\Cells\Blocks\Manage;

use App\Models\Manufacture\Cells\Block\BlockCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockTaskLineBlockCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'code_1c'        => $this->code_1c,
            'name'           => $this->name,
            'unit'           => $this->unit,
            'length'         => $this->length,
            'kdb'            => $this->kdb,
            'manuf_line'     => $this->line,
            'manuf_line_alt' => (string)($this->line_alt ?? BlockCollection::LINE_0),
            'priority'       => $this->priority,
            'productivity'   => $this->productivity,

            //'status'      => $this->status,
            //'comment'     => $this->comment,
            //'note'        => $this->note,
            //'meta'        => $this->meta,
            //'color'       => $this->color,
            //'created_at'  => $this->created_at,
            //'updated_at'  => $this->updated_at,
            //'collection'  => $this->collection,
            //'height'      => $this->height,

            //'_' => parent::toArray($request),
        ];
    }
}
