<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Model;

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
            'code_1c'        => $this->code_1c,
            'name'           => $this->name,
            'object_code_1c' => $this->object_code_1c,
            'object_name'    => $this->object_name,
            'text'           => $this->text,
            'text_vba'       => $this->text_vba,

            //'text'           => str_replace('\t', chr(9), str_replace('\n', chr(10), $this->text)),

            //'_' => parent::toArray($request),
        ];
        // return parent::toArray($request);
    }
}
