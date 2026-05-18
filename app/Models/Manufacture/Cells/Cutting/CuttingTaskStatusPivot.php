<?php

namespace App\Models\Manufacture\Cells\Cutting;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

// use Illuminate\Database\Eloquent\Model;

class CuttingTaskStatusPivot extends Pivot
{
    public $incrementing = true;

    public const TABLE = 'cutting_task_status_pivot';
    protected $table = self::TABLE;

    // __ Можно добавить хелпер для проверки, является ли запись "открытой"
    public function isOpen(): bool
    {
        return is_null($this->finished_at);
    }




    // Relations: Связь со СЗ
    public function cuttingTask(): BelongsTo
    {
        return $this->belongsTo(CuttingTask::class, 'cutting_task_id', 'id', CuttingTaskStatusPivot::TABLE);
    }

}
