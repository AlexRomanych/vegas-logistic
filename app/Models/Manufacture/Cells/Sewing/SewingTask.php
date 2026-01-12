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
    public const FIELD_UNIVERSAL = 'time_universal';
    public const FIELD_AUTO = 'time_auto';
    public const FIELD_SOLID_HARD = 'time_solid_hard';
    public const FIELD_SOLID_LITE = 'time_solid_lite';
    public const FIELD_UNDEFINED = 'time_undefined';
    public const FIELD_AVERAGE = 'time_average';

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
                SewingTaskStatus::class,                  // Класс, с которым связываемся
                'sewing_task_status_pivot',               // Промежуточная Таблица, связывающая классы
                'sewing_task_id',                         // Ключ в промежуточной таблице, связывающий с текущим классом
                'sewing_task_status_id')        // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            ->using(SewingTaskStatusPivot::class)
            ->withPivot(['started_at', 'finished_at', 'duration']);
    }


}
