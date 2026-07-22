<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $target
 * @property string $level
 * @property string $message
 * @property array $context
 */
class EventLog extends Model
{
    public const LEVEL_ERROR = 'ERROR';
    public const LEVEL_INFO = 'INFO';
    public const LEVEL_WARNING = 'WARNING';
    public const TARGET_EXPENSE = 'Expense';
    public const TARGET_CUTTING_TASK = 'CuttingTask';
    public const TARGET_BLOCK_TASK = 'BlockTask';
    public const TARGET_CUTTING_TASK_CUT = 'CuttingTaskCreator';
    public const TARGET_PARSE_ORDER_LINE_META_DATA = 'OrderLine';


    protected $guarded = false;

    protected $casts = [
        'context' => 'array',
    ];
}
