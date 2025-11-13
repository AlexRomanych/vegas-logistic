<?php

namespace App\Models\Plan;

use App\Models\Client;
use App\Models\Order\OrderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanLoad extends Model
{
    protected $guarded = false;

    protected $casts = [
        'amounts' => 'array',
    ];


    // Relations: Связь с клиентом
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Relations: Связь с Типом Заказа
    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

}
