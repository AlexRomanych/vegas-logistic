<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockTuningTime extends Model
{
    protected $guarded = false;

    protected $casts = [
        'tuning_time' => 'integer',
    ];

    // Relations: Рисунок, с которого происходит переналадка
    public function picFrom(): BelongsTo
    {
        return $this->belongsTo(BlockCollection::class, 'picture_from', 'id');
    }

    // Relations: Рисунок, на который происходит переналадка
    public function picTo(): BelongsTo
    {
        return $this->belongsTo(BlockCollection::class, 'picture_to', 'id');
    }
}
