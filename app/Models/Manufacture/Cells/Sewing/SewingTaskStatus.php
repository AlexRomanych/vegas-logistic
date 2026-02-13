<?php

namespace App\Models\Manufacture\Cells\Sewing;

use Illuminate\Database\Eloquent\Model;

class SewingTaskStatus extends Model
{
    public const SEWING_STATUS_CREATED_ID = 1;     // __ Создано
    public const SEWING_STATUS_ROLLING_ID = 2;     // __ Переходящий
    public const SEWING_STATUS_PENDING_ID = 3;     // __ Готово к выполнению
    public const SEWING_STATUS_RUNNING_ID = 4;     // __ Выполняется
    public const SEWING_STATUS_DONE_ID = 5;        // __ Выполнено

    // __ Все статусы
    public const SEWING_STATUSES = [
        self::SEWING_STATUS_CREATED_ID,
        self::SEWING_STATUS_ROLLING_ID,
        self::SEWING_STATUS_PENDING_ID,
        self::SEWING_STATUS_RUNNING_ID,
        self::SEWING_STATUS_DONE_ID,
    ];

    protected $guarded = false;
}
