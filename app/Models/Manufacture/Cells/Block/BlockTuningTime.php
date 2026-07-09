<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Relations\Pivot;
//use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockTuningTime extends Pivot
{
    protected $table = 'block_tuning_times';

    protected $guarded = false;

    protected $casts = [
        'tuning_time' => 'integer',
    ];

    //// Relations: Коллекция, с которого происходит переналадка
    //public function collectionFrom(): BelongsTo
    //{
    //    return $this->belongsTo(BlockCollection::class, 'collection_from', 'id');
    //}
    //
    //// Relations: Коллекция, на который происходит переналадка
    //public function collectionTo(): BelongsTo
    //{
    //    return $this->belongsTo(BlockCollection::class, 'collection_to', 'id');
    //}
}
