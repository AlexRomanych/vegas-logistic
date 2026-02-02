<?php

namespace App\Models\Manufacture\Cells\Sewing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SewingOperationSchema extends Model
{
    protected $guarded = [];


    // Relations: Связь со Статусами
    public function operations(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                SewingOperation::class,                  // Класс, с которым связываемся
                SewingOperationSchemaPivot::TABLE,               // Промежуточная Таблица, связывающая классы
                'sewing_operation_schema_id',                         // Ключ в промежуточной таблице, связывающий с текущим классом
                'sewing_operation_id' // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            )
            ->using(SewingOperationSchemaPivot::class)
            ->withPivot(['ratio', 'amount', 'position', 'condition']);
    }


}
