<?php

namespace App\Models\Manufacture\Cells\Sewing;
use Illuminate\Database\Eloquent\Relations\Pivot;
// use Illuminate\Database\Eloquent\Model;

class SewingTaskStatusPivot extends Pivot
{
    protected $table = 'sewing_task_status_pivot';

}
