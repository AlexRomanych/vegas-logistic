<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method query()
 * @method own(mixed $query)
 */
class Block extends Model
{
    //// __ Имя колонки первичного ключа (если это не 'id')
    //protected $primaryKey = 'code_1c';
    //
    //// __ Указываем, что тип ключа — строка
    //protected $keyType = 'string';
    //
    //// __ Отключаем автоинкремент (Laravel по умолчанию пытается привести ключ к int)
    //public $incrementing = false;

    protected $guarded = false;

    protected $casts = [
        'active' => 'boolean',
        'line'   => 'integer',
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
            ->whereHas('blockCollection', function (Builder $collectionQuery) {
                $collectionQuery
                    ->where('active', true) // Коллекция должна быть активна
                    ->where('own', true);   // Коллекция должна быть собственного производства
            });
    }


    // Relations: Связь с группой (коллекцией) блоков
    public function blockCollection(): BelongsTo
    {
        return $this->belongsTo(BlockCollection::class, 'collection', CODE_1C);
    }

    // Relations: Связь с Блоком подмены
    public function substitutionBlock(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'substitution_block_code_1c', CODE_1C);
    }

}
