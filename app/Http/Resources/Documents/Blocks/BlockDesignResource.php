<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Documents\Blocks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockDesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'kdb'         => $this->kdb,
            'name'        => $this->kdb,
            'file_path'   => $this->file_path,
            'description' => $this->description,
            //'created_at'  => $this->created_at,
            //'updated_at'  => $this->updated_at,

            //'_' => parent::toArray($request),
        ];
    }
}
