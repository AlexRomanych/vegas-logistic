<?php

namespace App\Models\Manufacture\Cells\Sewing;

use Illuminate\Database\Eloquent\Model;

class SewingTaskStatus extends Model
{
    public const SEWING_STATUS_CREATED_ID = 1;     // Создано
    public const SEWING_STATUS_PENDING_ID = 2;     // Готово к выполнению
    public const SEWING_STATUS_RUNNING_ID = 3;     // Выполняется
    public const SEWING_STATUS_DONE_ID = 4;        // Выполнено


    protected $guarded = false;
}
