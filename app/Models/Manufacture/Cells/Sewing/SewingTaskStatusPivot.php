<?php

namespace App\Models\Manufacture\Cells\Sewing;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

// use Illuminate\Database\Eloquent\Model;

class SewingTaskStatusPivot extends Pivot
{
    public $incrementing = true;

    public const TABLE = 'sewing_task_status_pivot';
    protected $table = self::TABLE;

    // __ Можно добавить хелпер для проверки, является ли запись "открытой"
    public function isOpen(): bool
    {
        return is_null($this->finished_at);
    }




    // Relations: Связь со СЗ
    public function sewingTask(): BelongsTo
    {
        return $this->belongsTo(SewingTask::class, 'sewing_task_id', 'id', SewingTaskStatusPivot::TABLE);
    }

}
