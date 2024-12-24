<?php

namespace App\Models\Manufacture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CellItem extends Model
{

    //attract: Привязываем к группе ПЯ
    public function cellsGroup(): BelongsTo
    {
        return $this->belongsTo(CellsGroup::class);
    }

    //attract: Привязываем группу к норме
    public function CellNorm(): HasOne
    {
        return $this->hasOne(CellNorm::class);
    }
}
