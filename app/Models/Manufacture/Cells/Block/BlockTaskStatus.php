<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Model;

class BlockTaskStatus extends Model
{
    public const BLOCK_STATUS_CREATED_ID = 1;     // __ Создано
    public const BLOCK_STATUS_ROLLING_ID = 2;     // __ Переходящий
    public const BLOCK_STATUS_PENDING_ID = 3;     // __ Готово к выполнению
    public const BLOCK_STATUS_RUNNING_ID = 4;     // __ Выполняется
    public const BLOCK_STATUS_DONE_ID = 5;        // __ Выполнено

    // __ Все статусы
    public const BLOCK_STATUSES = [
        self::BLOCK_STATUS_CREATED_ID,
        self::BLOCK_STATUS_ROLLING_ID,
        self::BLOCK_STATUS_PENDING_ID,
        self::BLOCK_STATUS_RUNNING_ID,
        self::BLOCK_STATUS_DONE_ID,
    ];

    protected $guarded = false;
}
