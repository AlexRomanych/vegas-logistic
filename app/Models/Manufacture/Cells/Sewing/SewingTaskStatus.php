<?php

namespace App\Models\Manufacture\Cells\Sewing;

use Illuminate\Database\Eloquent\Model;

class SewingTaskStatus extends Model
{
    public const SEWING_STATUS_CREATED_ID = 1;     // Создано
    public const SEWING_STATUS_ROLLING_ID = 2;     // Переходящий
    public const SEWING_STATUS_PENDING_ID = 3;     // Готово к выполнению
    public const SEWING_STATUS_RUNNING_ID = 4;     // Выполняется
    public const SEWING_STATUS_DONE_ID = 5;        // Выполнено


    protected $guarded = false;
}
