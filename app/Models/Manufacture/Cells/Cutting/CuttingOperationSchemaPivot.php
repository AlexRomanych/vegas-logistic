<?php

namespace App\Models\Manufacture\Cells\Cutting;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CuttingOperationSchemaPivot extends Pivot
{
    const TABLE = 'cutting_operation_schema_pivot';

    protected $table = self::TABLE;


}
