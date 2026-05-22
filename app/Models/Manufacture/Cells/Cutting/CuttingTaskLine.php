<?php

namespace App\Models\Manufacture\Cells\Cutting;

use App\Models\Order\OrderLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuttingTaskLine extends Model
{
    // --- Константы

    // --- Поля
    public const FIELD_TABLE_1 = 'table_1';
    public const FIELD_TABLE_2 = 'table_2';
    public const FIELD_TABLE_3 = 'table_3';
    public const FIELD_AVERAGE = 'average';
    public const FIELD_UNDEFINED = 'undefined';

    protected $guarded = false;

    protected $casts = [
        'time_labor'    => 'array',
        'phantom_json'  => 'array',
        'false_history' => 'array',
    ];

    // Relations: Связь с Контекстом Заявки (OrderLine)
    public function orderLine(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class);
    }


    // Relations: Cвязь с самим собой (Боковина)
    public function details(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
