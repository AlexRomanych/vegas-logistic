<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLine extends Model
{
    protected $guarded = false;


    // Relations: Связь с Заказом
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id', 'orders');
    }

    // Relations: Связь с Моделью
    public function model(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Models\Model::class, 'model_code_1c', CODE_1C);
    }

}
