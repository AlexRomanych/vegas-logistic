<?php

namespace App\Models\Manufacture;

use App\Models\Abstract\Cell;
use App\Models\Order\Line;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//use Illuminate\Database\Eloquent\Model;

class CellItem extends Cell
{

//    protected $guarded = [];
//
//    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
//
//    protected $with = ['line'];
//
//    //attract: Задаем вычисляемые свойства
//    protected $appends = ['norm'];    // Задаем норму
//
//    //attract: Привязываем к самой линии в заказе
//    public function line(): BelongsTo
//    {
//        return $this->belongsTo(Line::class);
//    }
}
