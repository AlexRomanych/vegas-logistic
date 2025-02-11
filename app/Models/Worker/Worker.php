<?php

namespace App\Models\Worker;

use App\Models\Manufacture\CellItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Worker extends Model
{
    public $guarded = [];

    public function cellItem(): BelongsTo
    {
        return $this->belongsTo(CellItem::class);
    }

}

