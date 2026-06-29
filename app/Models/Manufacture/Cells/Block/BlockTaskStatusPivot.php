<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
// use Illuminate\Database\Eloquent\Model;

class BlockTaskStatusPivot extends Pivot
{
    public $incrementing = true;

    public const TABLE = 'block_task_status_pivot';
    protected $table = self::TABLE;

    // __ Можно добавить хелпер для проверки, является ли запись "открытой"
    public function isOpen(): bool
    {
        return is_null($this->finished_at);
    }

    // Relations: Связь со СЗ
    public function blockTask(): BelongsTo
    {
        return $this->belongsTo(BlockTask::class, 'block_task_id', 'id', BlockTaskStatusPivot::TABLE);
    }

}
