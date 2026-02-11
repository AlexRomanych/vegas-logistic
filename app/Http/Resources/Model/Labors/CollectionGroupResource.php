<?php

namespace App\Http\Resources\Model\Labors;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // __ $this в данном случае — это массив/коллекция,
        // __ где ключ — название коллекции, а значение — список моделей
        return $this->map(function ($models, $collectionName) {
            return [
                'collection' => $collectionName,
                'items' => ModelLaborResource::collection($models),  // __ Используем ресурс для самих моделей
            ];
        })
            ->values() // __ values() сбросит ключи в массив [0, 1, 2]
            ->all();   // <--- КРИТИЧНО: превращаем коллекцию обратно в массив
    }
}
