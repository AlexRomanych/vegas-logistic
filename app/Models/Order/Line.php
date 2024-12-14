<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Line extends Model
{

    protected $fillable = [
        'order_id',
        'load_1C',
        'size', 'amount', 'model', 'textile', 'composition',
        'comment', 'describe_1', 'describe_2', 'describe_3',

//        'check_assembly', 'moment_assembly',
//        'check_sewing', 'moment_sewing',
//
//        'check_cutting', 'moment_cutting',
//
//        'model_code1C',
//
//        'assembly_part_id', 'sewing_part_id', 'cutting_part_id',
    ];

    protected $hidden = [
        'model_code1C',
        'created_at', 'updated_at',
    ];

//    protected $with = [
//        'model'
//    ];




// Отношение к Заказу
    public function Order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }


//    public function model(): HasOne
//    {
//        return $this->hasOne(\App\Models\Model::class, 'code1C', 'model_code1C');
//    }

}
