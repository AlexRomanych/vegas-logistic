<?php

namespace App\Models\Manufacture\Cells\Sewing;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SewingDayWorkerPivot extends Pivot
{
    const TABLE = 'sewing_day_worker_pivot';

    protected $table = self::TABLE;


}
