<?php

namespace App\Models\Order;

use App\Models\Logs\EventLog;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskLine;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskLine;
use App\Models\Materials\Material;
use App\Models\Models\ModelConstruct;
use App\Services\OrdersService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;


/**
 * @property string $model_code_1c
 * @property int $width
 * @property int $length
 * @property int $height
 * @property array $meta_data
 */
class OrderLine extends Model
{
    public const BLOCK_META_FIELD = 'block_meta';

    protected $guarded = false;


    // __ Attribute. Возвращаем Мета-дату по Высоте Чехла и Столу Раскроя
    protected function metaData(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return OrdersService::getOrderLineMetaData($attributes);
            },
        );
    }


    // Relations: Связь с Заказом
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id', 'orders');
    }

    // Relations: Связь с Моделью
    public function model(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Models\Model::class, 'model_code_1c', CODE_1C);
        //return $this->belongsTo(\App\Models\Models\Model::class, 'model_code_1c', CODE_1C);
    }

    // Relations: Связь с Материалами
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(
            Material::class,                // 1. Модель, с которой связываемся
            'order_line_material_pivot',    // 2. Имя пивот-таблицы в базе данных
            'order_line_id',                // 3. Внешний ключ текущей модели в пивоте
            'material_code_1c'                   // 4. Внешний ключ связанной модели в пивоте
        )
            ->using(OrderLineMaterialPivot::class) // 5. Указываем использовать кастомную пивот-модель
            ->withPivot(['expense_per_pic', 'rest_per_pic', 'id', 'active']);
        //->withTimestamps();                    // 6. Если в пивот-таблице есть поля created_at/updated_at
    }

    // Relations: Связь со Спецификацией, котрая подгружается из 1С
    public function specification(): BelongsTo
    {
        return $this->belongsTo(ModelConstruct::class, 'construct_code_1c', CODE_1C);
    }

    // Relations: Связь с дополнительной Спецификацией, котрая подгружается из 1С
    public function specificationAdd(): BelongsTo
    {
        return $this->belongsTo(ModelConstruct::class, 'construct_add_code_1c', CODE_1C);
    }

    // Relations: Связь со Строкой CuttingTaskLines (Обратная связь получения строки Заявки в Заказе)
    public function cuttingTaskLine(): HasMany
    {
        return $this->hasMany(CuttingTaskLine::class, 'order_line_id', 'id');
    }

    // Relations: Связь со Строкой AssemblyTaskLines (Обратная связь получения строки Заявки в Заказе)
    public function assemblyTaskLine(): HasMany
    {
        return $this->hasMany(AssemblyTaskLine::class, 'order_line_id', 'id');
    }
}
