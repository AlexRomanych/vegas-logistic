<?php

namespace App\Models\Manufacture\Cells\sewing;

use App\Models\Manufacture\CellItem;
use App\Models\Norm\sewing\CellSewingAutoNorm;
use App\Models\Order\Line;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//use Illuminate\Database\Eloquent\Model;

class CellSewingHard extends CellItem
{
    protected CellSewingAutoNorm $norm;                 // attract: todo Не забыть поменять норму для УШМ

    protected $table = 'cell_sewing_hard';


    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $with = ['line'];


    //attract: Задаем вычисляемые свойства
    protected $appends = ['norm'];    // Задаем норму


    public function __construct()
    {
        parent::__construct();
        $this->norm = new CellSewingAutoNorm();     // добавляем объект норм
    }

//info получаем время изготовления чехла АШМ
    public function getNormAttribute(): float
    {
        return $this->norm->calculateNorm(
            size: $this->line->size,
            modelName: $this->line->model,
            amount: (int)$this->amount);
    }


    //attract: Привязываем к самой линии в заказе
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

}
