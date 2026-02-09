<?php

namespace App\Models\Worker;

use App\Models\Manufacture\CellItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Worker extends Model
{
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];


    // ___ Scopes
    public function scopeActive($query)
    {
        // __ Возвращаем только активных сотрудников
        return $query->where('active', true);
    }



    // Relations: Связь с Производственной ячейкой
    public function cellItem(): BelongsTo
    {
        return $this->belongsTo(CellItem::class);
    }

}

