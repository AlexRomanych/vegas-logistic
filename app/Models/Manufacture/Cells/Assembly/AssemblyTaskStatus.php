<?php

namespace App\Models\Manufacture\Cells\Assembly;

use Illuminate\Database\Eloquent\Model;

class AssemblyTaskStatus extends Model
{
    public const ASSEMBLY_STATUS_CREATED_ID = 1;     // __ Создано
    public const ASSEMBLY_STATUS_ROLLING_ID = 2;     // __ Переходящий
    public const ASSEMBLY_STATUS_PENDING_ID = 3;     // __ Готово к выполнению
    public const ASSEMBLY_STATUS_RUNNING_ID = 4;     // __ Выполняется
    public const ASSEMBLY_STATUS_DONE_ID = 5;        // __ Выполнено

    // __ Все статусы
    public const ASSEMBLY_STATUSES = [
        self::ASSEMBLY_STATUS_CREATED_ID,
        self::ASSEMBLY_STATUS_ROLLING_ID,
        self::ASSEMBLY_STATUS_PENDING_ID,
        self::ASSEMBLY_STATUS_RUNNING_ID,
        self::ASSEMBLY_STATUS_DONE_ID,
    ];

    protected $guarded = false;
}
