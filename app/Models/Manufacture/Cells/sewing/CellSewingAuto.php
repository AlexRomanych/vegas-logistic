<?php

namespace App\Models\Manufacture\Cells\sewing;

use App\Models\Abstract\Cell;
use App\Models\Order\Line;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//use Illuminate\Database\Eloquent\Model;

class CellSewingAuto extends Cell
{
    protected $table = 'cell_sewing_auto';

    protected $guarded = [];

    protected $with = ['line'];

    //attract: Привязываем к самой линии в заказе
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

}
