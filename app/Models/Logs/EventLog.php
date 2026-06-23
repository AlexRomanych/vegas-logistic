<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    public const LEVEL_ERROR = 'ERROR';
    public const LEVEL_INFO = 'INFO';
    public const LEVEL_WARNING = 'WARNING';
    public const TARGET_EXPENSE = 'Expense';
    public const TARGET_CUTTING_TASK = 'CuttingTask';


    protected $guarded = false;

    protected $casts = [
        'context' => 'array',
    ];
}
