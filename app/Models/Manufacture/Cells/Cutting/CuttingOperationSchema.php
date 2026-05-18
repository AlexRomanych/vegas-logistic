<?php

namespace App\Models\Manufacture\Cells\Cutting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CuttingOperationSchema extends Model
{
    protected $guarded = [];


    // Relations: Связь с Операциями
    public function operations(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                CuttingOperation::class,                 // Класс, с которым связываемся
                CuttingOperationSchemaPivot::TABLE,       // Промежуточная Таблица, связывающая классы
                'cutting_operation_schema_id',            // Ключ в промежуточной таблице, связывающий с текущим классом
                'cutting_operation_id'           // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            )
            ->using(CuttingOperationSchemaPivot::class)
            ->withPivot(['ratio', 'amount', 'position', 'condition']);
    }


}
