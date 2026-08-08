<?php

namespace App\Models\Manufacture\Cells\Assembly;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

// use Illuminate\Database\Eloquent\Model;

class AssemblyTaskStatusPivot extends Pivot
{
    public $incrementing = true;

    public const TABLE = 'assembly_task_status_pivot';
    protected $table = self::TABLE;

    // __ Можно добавить хелпер для проверки, является ли запись "открытой"
    public function isOpen(): bool
    {
        return is_null($this->finished_at);
    }



    // Relations: Связь со СЗ
    public function assemblyTask(): BelongsTo
    {
        return $this->belongsTo(AssemblyTask::class, 'assembly_task_id', 'id', AssemblyTaskStatusPivot::TABLE);
    }

}
