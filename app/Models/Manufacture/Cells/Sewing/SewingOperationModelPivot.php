<?php

namespace App\Models\Manufacture\Cells\Sewing;
use Illuminate\Database\Eloquent\Relations\Pivot;
// use Illuminate\Database\Eloquent\Model;

class SewingOperationModelPivot extends Pivot
{
    const TABLE = 'sewing_operation_model_pivot';

    protected $table = self::TABLE;

}
