<?php

namespace App\Models\Manufacture\Cells\Block;

use App\Models\Manufacture\Documents\BlockDesignDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
//use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder|BlockCollection query()
 * @method static Builder|BlockCollection actual()
 */
class BlockCollection extends Model
{
    public const LINE_0 = 0;
    public const LINE_1 = 1;
    public const LINE_2 = 2;

    public const UNIT = '';
    public const UNIT_PIC = 'шт.';
    public const UNIT_METERS = 'м.п.';


    protected $guarded = false;

    protected $casts = [
        'active'       => 'boolean',
        'priority'     => 'integer',
        'line'         => 'string',
        'line_alt'     => 'string',
        'productivity' => 'float',
        'height'       => 'integer',
        'length'       => 'integer',
        'own'          => 'boolean',
    ];


    // Scopes: Выбор актуальной записи
    public function scopeActual(Builder $query): Builder
    {
        return $query
            ->where('active', true)
            ->where('own', true);
    }


    // Relations: Связь с блоками
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'collection', CODE_1C);
    }

    // Relations: Связь с КДБ
    public function kdbDoc(): BelongsTo
    {
        return $this->belongsTo(BlockDesignDocument::class, 'kdb', 'kdb');
        //return $this->hasOne(BlockDesignDocument::class, 'kdb', 'kdb');
    }

    // Relations: Коллекция Блоков, с которой происходит переналадка
    public function collectionsFrom(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                BlockCollection::class,
                'block_tuning_times',
                'collection_to',
                'collection_from',
                'id'
            )
            ->using(BlockTuningTime::class)
            ->withPivot(['tuning_time']);
    }

    // Relations: Коллекция Блоков, на которую происходит переналадка
    public function collectionsTo(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                BlockCollection::class,
                'block_tuning_times',
                'collection_from',
                'collection_to',
                'id'
            )
            ->using(BlockTuningTime::class)
            ->withPivot(['tuning_time']);
    }

}
