<?php

namespace App\Models\Manufacture\Cells\Cutting;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CuttingDayWorkerPivot extends Pivot
{
    const TABLE = 'cutting_day_worker_pivot';

    protected $table = self::TABLE;


}
