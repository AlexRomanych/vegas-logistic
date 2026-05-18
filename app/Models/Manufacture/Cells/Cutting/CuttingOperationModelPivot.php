<?php

namespace App\Models\Manufacture\Cells\Cutting;
use Illuminate\Database\Eloquent\Relations\Pivot;
// use Illuminate\Database\Eloquent\Model;

class CuttingOperationModelPivot extends Pivot
{
    const TABLE = 'cutting_operation_model_pivot';

    protected $table = self::TABLE;

}
