<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTuningTime extends Model
{
    protected $guarded = false;

    // Relations: Рисунок, с которого происходит переналадка
    public function picturesFrom(): BelongsTo
    {
        return $this->belongsTo(FabricPicture::class, 'picture_from', 'id');
    }

    // Relations: Рисунок, на который происходит переналадка
    public function picturesTo(): BelongsTo
    {
        return $this->belongsTo(FabricPicture::class, 'picture_to', 'id');
    }

}
