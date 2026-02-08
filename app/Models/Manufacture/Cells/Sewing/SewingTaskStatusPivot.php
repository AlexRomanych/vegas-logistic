<?php

namespace App\Models\Manufacture\Cells\Sewing;

use Illuminate\Database\Eloquent\Relations\Pivot;

// use Illuminate\Database\Eloquent\Model;

class SewingTaskStatusPivot extends Pivot
{
    public $incrementing = true;
    protected $table = 'sewing_task_status_pivot';

    // __ Можно добавить хелпер для проверки, является ли запись "открытой"
    public function isOpen(): bool
    {
        return is_null($this->finished_at);
    }
}
