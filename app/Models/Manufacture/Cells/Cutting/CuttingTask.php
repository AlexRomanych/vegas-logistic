<?php

namespace App\Models\Manufacture\Cells\Cutting;

use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuttingTask extends Model
{
    protected $guarded = false;
    protected $casts = [
        'action_at' => 'datetime',
    ];


    // --- Константы

    // --- Поля
    public const FIELD_UNIVERSAL = 'universal';
    public const FIELD_AUTO = 'auto';
    public const FIELD_SOLID_HARD = 'solid_hard';
    public const FIELD_SOLID_LITE = 'solid_lite';
    public const FIELD_UNDEFINED = 'undefined';
    public const FIELD_AVERAGE = 'average';


    // --- -------------------------------

    // --- -------------------------------
    // --- ---------- Scopes -------------
    // --- -------------------------------
    public function scopeByStatus($query, array|string|int $statusIds = null)
    {
        if (empty($statusIds)) return $query;

        $statusIds = collect($statusIds)->flatten()->map(fn($id) => (int)$id)->toArray();
        // $statusIds = is_string($statusIds) ? [(int) $statusIds] : [$statusIds];

        // Магия: join-им только ОДНУ последнюю запись из истории статусов
        return $query->whereHas('latestTaskStatus', function ($q) use ($statusIds) {
            $q->whereIn('cutting_task_status_id', $statusIds);
        });


        // return $query->whereHas('statuses', function ($q) use ($statusIds) {
        //     // Мы фильтруем прямо по полю status_id в промежуточной таблице
        //     // Это быстрее, так как не нужно джойнить таблицу statuses
        //     $q->whereIn('cutting_task_status_id', $statusIds);
        // });
    }

    // ___ Поиск по дате
    public function scopeWhereDayAt($query, string|Carbon $inDate)
    {
        // __ Важный нюанс:
        // __ В твоем исходном коде использовался метод whereDate().
        // __ Однако whereDate в SQL принудительно преобразует поле базы данных к формату YYYY-MM-DD
        // __ (в PostgreSQL это делает функция DATE()), что убивает использование индексов по полю action_at.
        // __ Поскольку ты передаешь полноценные startOfDay() и endOfDay() (со временем 00:00:00 и 23:59:59),
        // __ правильнее и гораздо быстрее использовать обычный where().
        // __ Это заставит базу использовать индекс и ускорит выборку.

        // __ Если пришла строка, парсим её в Carbon, если уже Carbon — работаем с клоном,
        // __ чтобы случайно не мутировать исходный объект даты в коде
        $targetDate = is_string($inDate) ? Carbon::parse($inDate) : $inDate->copy();

        return $query
            ->whereDate('action_at', '>=', $targetDate->startOfDay())
            ->whereDate('action_at', '<=', $targetDate->endOfDay());
    }



    // Relations: Связь с Основной Заявкой
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }


    // Relations: Связь с содержимым (строками)
    public function cuttingLines(): HasMany
    {
        return $this
            ->hasMany(CuttingTaskLine::class, 'cutting_task_id')
            ->orderBy('position');
    }


    // Relations: Связь со Статусами
    public function statuses(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                CuttingTaskStatus::class,         // Класс, с которым связываемся
                CuttingTaskStatusPivot::TABLE,      // Промежуточная Таблица, связывающая классы
                'cutting_task_id',                // Ключ в промежуточной таблице, связывающий с текущим классом
                'cutting_task_status_id' // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            )
            ->using(CuttingTaskStatusPivot::class)
            ->withPivot(['id', 'set_at', 'started_at', 'finished_at', 'duration', 'created_at', 'updated_at']);
    }


    // Relations: Связь с последним статусом
    public function latestTaskStatus()
    {
        // Указываем, что последнюю запись ищем по максимальному ID в пивоте
        return $this->hasOne(CuttingTaskStatusPivot::class)->latestOfMany('id');

        // ИЛИ, если хочешь по дате создания:
        // return $this->hasOne(TaskStatus::class)->latestOfMany('created_at');
    }

    // Relations: Связь с последним статусом
    public function latestTaskStatusByDate()
    {
        // Указываем, что последнюю запись ищем по максимальному ID в пивоте
        return $this->hasOne(CuttingTaskStatusPivot::class)->latestOfMany('created_at');

        // ИЛИ, если хочешь по дате создания:
        // return $this->hasOne(TaskStatus::class)->latestOfMany('created_at');
    }

}
