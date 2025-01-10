<?php

namespace App\Models\Manufacture\Cells\Sewing;

use App\Models\Manufacture\CellItem;
use App\Models\Norm\sewing\CellSewingAutoNorm;
use App\Models\Order\Line;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//use Illuminate\Database\Eloquent\Model;

class CellSewingAuto extends CellItem
{
    protected $table = 'cell_sewing_auto';

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $with = ['line'];


    //attract: Задаем вычисляемые свойства
    protected $appends = ['norm'];    // Задаем норму

    //info получаем время изготовления чехла АШМ


    public function getNormAttribute(): float
    {
        $norm = new CellSewingAutoNorm();

//        return 1.21;

        return $norm->calculateNorm(
            size: $this->line->size,
            modelName: $this->line->model,
            amount: (int) $this->line->model);
    }


    //attract: Привязываем к самой линии в заказе
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

}
