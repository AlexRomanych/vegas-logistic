<?php

namespace App\Models\Order;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderLineMaterialPivot extends Pivot
{
    protected $table = 'order_line_material_pivot';

    protected $casts = [
        'expense_per_pic' => 'float',
        'rest_per_pic'    => 'float',
        'expense'         => 'float',
        'rest'            => 'float',
    ];

}
