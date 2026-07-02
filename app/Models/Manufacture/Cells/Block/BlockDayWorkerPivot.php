<?php

namespace App\Models\Manufacture\Cells\Block;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BlockDayWorkerPivot extends Pivot
{
    const TABLE = 'block_day_worker_pivot';

    protected $table = self::TABLE;


}
