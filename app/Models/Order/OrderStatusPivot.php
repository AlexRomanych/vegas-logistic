<?php

namespace App\Models\Order;
use Illuminate\Database\Eloquent\Relations\Pivot;
// use Illuminate\Database\Eloquent\Model;

class OrderStatusPivot extends Pivot
{
    protected $table = 'order_status_pivot';

}
