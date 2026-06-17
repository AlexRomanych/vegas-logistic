<?php

namespace App\Models\Order;

use App\Models\Materials\Material;
use App\Models\Models\ModelConstruct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderLine extends Model
{
    protected $guarded = false;


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
            ->withPivot(['expense_per_pic', 'rest_per_pic']);
            //->withTimestamps();                    // 6. Если в пивот-таблице есть поля created_at/updated_at
    }

    // Relations: Связь со спецификацией, котрая подгружается из 1С
    public function specification(): BelongsTo
    {
        return $this->belongsTo(ModelConstruct::class, 'construct_code_1c', CODE_1C);
    }

}
