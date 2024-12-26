<?php

namespace App\Models\Manufacture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CellNorm extends Model
{

    protected $guarded = [];

    //attract: связываем с ПЯ
    public function cellItem(): BelongsTo
    {
        return $this->belongsTo(CellItem::class);
    }
}
