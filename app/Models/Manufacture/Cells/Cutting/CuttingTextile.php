<?php

namespace App\Models\Manufacture\Cells\Cutting;

use Illuminate\Database\Eloquent\Model;

class CuttingTextile extends Model
{
    // 1. Указываем имя колонки первичного ключа (если оно отличается от 'id')
    protected $primaryKey = CODE_1C;

    // 2. Отключаем автоинкремент (ОБЯЗАТЕЛЬНО)
    public $incrementing = false;

    // 3. Указываем тип данных ключа (например, 'string', если это UUID или текстовый код)
    // Если это всё же число (int), которое ты пишешь вручную, укажи 'int'
    protected $keyType = 'string';

    protected $guarded = false;

    protected $casts = [
        'active'     => 'boolean',
        'layers'     => 'integer',
        'width'      => 'integer',
        'width_work' => 'integer',
    ];

}
