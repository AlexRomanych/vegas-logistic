<?php

namespace App\Models\Manufacture\Cells\Sewing;

use App\Models\Order\OrderLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SewingTaskLine extends Model
{
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
}
