<?php

namespace App\Models\Manufacture\Cells;

use App\Models\Abstract\Cell;
use App\Models\Order\Line;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CellSewingAuto extends Cell
{
    protected $table = 'cell_sewing_auto';

    //attract: Привязываем к самой линии в заказе
    public function Line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }
}
