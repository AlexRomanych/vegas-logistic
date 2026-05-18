<?php

namespace App\Models\Manufacture\Cells\Cutting;

use Illuminate\Database\Eloquent\Model;

class CuttingTaskStatus extends Model
{
    public const CUTTING_STATUS_CREATED_ID = 1;     // __ Создано
    public const CUTTING_STATUS_ROLLING_ID = 2;     // __ Переходящий
    public const CUTTING_STATUS_PENDING_ID = 3;     // __ Готово к выполнению
    public const CUTTING_STATUS_RUNNING_ID = 4;     // __ Выполняется
    public const CUTTING_STATUS_DONE_ID = 5;        // __ Выполнено

    // __ Все статусы
    public const CUTTING_STATUSES = [
        self::CUTTING_STATUS_CREATED_ID,
        self::CUTTING_STATUS_ROLLING_ID,
        self::CUTTING_STATUS_PENDING_ID,
        self::CUTTING_STATUS_RUNNING_ID,
        self::CUTTING_STATUS_DONE_ID,
    ];

    protected $guarded = false;
}
