<?php

namespace App\Models\Manufacture\Events;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder|CellEvent query()
 * @method Builder|CellEvent dayEvents(int $dayId, string $cell)
 */
class CellEvent extends Model
{
    protected $guarded = false;

    public const CELL_UNKNOWN = 'unknown';
    public const CELL_BLOCKS = 'blocks';
    public const CELL_SEWING = 'sewing';
    public const CELL_CUTTING = 'cutting';
    public const CELL_FABRIC = 'fabric';

    protected $casts = [
        'start_at'  => 'datetime',
        'finish_at' => 'datetime',
        'day_id'    => 'integer',
    ];

    // Scopes Получаем Журнал для Данной Производственной Ячейки
    public function scopeDayEvents(Builder $query, int $dayId, string $cell): Builder
    {
        return $query
            ->where('day_id', $dayId)
            ->where('cell', $cell);
    }


}
