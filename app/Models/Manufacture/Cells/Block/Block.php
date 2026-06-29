<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Block extends Model
{
    protected $guarded = false;

    protected $casts = [
        'active' => 'boolean',
        'width'  => 'integer',
        'length' => 'integer',
        'height' => 'integer',
    ];

    // --- Scope

    // --- Выбираем Активные Блоки Собственного Производства
    public function scopeOwn(Builder $query): Builder
    {
        return $query
            // __ Проверяем, что сам блок активен
            ->where('active', true)
            // __ Проваливаемся в проверку связанной коллекции
            ->whereHas('collection', function (Builder $collectionQuery) {
                $collectionQuery
                    ->where('active', true) // Коллекция должна быть активна
                    ->where('own', true);   // Коллекция должна быть собственного производства
            });
    }


    // Relations: Связь с группой (коллекцией) блоков
    public function collection(): BelongsTo
    {
        return $this->belongsTo(BlockCollection::class, 'collection', CODE_1C);
    }

}
