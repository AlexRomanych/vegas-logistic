<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTuningTime extends Model
{
    protected $guarded = false;

    protected $casts = [
        'tuning_time' => 'integer',
    ];

    // Relations: Рисунок, с которого происходит переналадка
    public function picFrom(): BelongsTo
    {
        return $this->belongsTo(FabricPicture::class, 'picture_from', 'id');
    }

    // Relations: Рисунок, на который происходит переналадка
    public function picTo(): BelongsTo
    {
        return $this->belongsTo(FabricPicture::class, 'picture_to', 'id');
    }

}
