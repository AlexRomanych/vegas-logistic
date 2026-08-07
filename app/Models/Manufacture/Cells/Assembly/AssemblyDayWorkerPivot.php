<?php

namespace App\Models\Manufacture\Cells\Assembly;

use Illuminate\Database\Eloquent\Relations\Pivot;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssemblyDayWorkerPivot extends Pivot
{
    const TABLE = 'assembly_day_worker_pivot';

    protected $table = self::TABLE;

}
