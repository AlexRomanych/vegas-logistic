<?php

namespace App\Models\Manufacture\Cells\Sewing;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SewingTask extends Model
{
    protected $guarded = false;

    // --- Константы

    // --- Поля
    public const FIELD_UNIVERSAL = 'universal';
    public const FIELD_AUTO = 'auto';
    public const FIELD_SOLID_HARD = 'solid_hard';
    public const FIELD_SOLID_LITE = 'solid_lite';
    public const FIELD_UNDEFINED = 'undefined';
    public const FIELD_AVERAGE = 'average';

    // --- -------------------------------


    // Relations: Связь с Основной Заявкой
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }


    // Relations: Связь с содержимым (строками)
    public function sewingLines(): HasMany
    {
        return $this->hasMany(SewingTaskLine::class, 'sewing_task_id');
    }


    // Relations: Связь со Статусами
    public function statuses(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                SewingTaskStatus::class,         // Класс, с которым связываемся
                'sewing_task_status_pivot',      // Промежуточная Таблица, связывающая классы
                'sewing_task_id',                // Ключ в промежуточной таблице, связывающий с текущим классом
                'sewing_task_status_id' // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            )
            ->using(SewingTaskStatusPivot::class)
            ->withPivot(['id', 'set_at', 'started_at', 'finished_at', 'duration', 'created_at', 'updated_at']);
    }




}
