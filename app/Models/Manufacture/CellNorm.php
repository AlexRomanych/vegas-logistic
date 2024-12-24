<?php

namespace App\Models\Manufacture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CellNorm extends Model
{

    //attract: связываем с ПЯ
    public function CellItem(): BelongsTo
    {
        return $this->belongsTo(CellItem::class);
    }
}
