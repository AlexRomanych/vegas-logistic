<?php

namespace App\Models\Manufacture\Cells\Block;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class BlockTask extends Model
{
    protected $guarded = false;


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
            $q->whereIn('block_task_status_id', $statusIds);
        });


        // return $query->whereHas('statuses', function ($q) use ($statusIds) {
        //     // Мы фильтруем прямо по полю status_id в промежуточной таблице
        //     // Это быстрее, так как не нужно джойнить таблицу statuses
        //     $q->whereIn('block_task_status_id', $statusIds);
        // });
    }

    // --- Поиск по дате
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
            ->where('action_at', '>=', $targetDate->startOfDay())
            ->where('action_at', '<=', $targetDate->endOfDay());
        //->whereDate('action_at', '>=', $targetDate->startOfDay())
        //->whereDate('action_at', '<=', $targetDate->endOfDay());
    }



    // Relations: Связь с Основной Заявкой
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }


    // Relations: Связь с содержимым (строками)
    public function blockLines(): HasMany
    {
        return $this
            ->hasMany(BlockTaskLine::class, 'block_task_id')
            ->orderBy('position');
    }

    public function lines(): HasMany
    {
        return $this
            ->hasMany(blockTaskLine::class, 'block_task_id')
            ->orderBy('position');
    }


    // Relations: Связь со Статусами
    public function statuses(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                BlockTaskStatus::class,         // Класс, с которым связываемся
                BlockTaskStatusPivot::TABLE,      // Промежуточная Таблица, связывающая классы
                'block_task_id',                // Ключ в промежуточной таблице, связывающий с текущим классом
                'block_task_status_id' // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            )
            ->using(BlockTaskStatusPivot::class)
            ->withPivot(['id', 'set_at', 'started_at', 'finished_at', 'duration', 'created_at', 'updated_at']);
    }


    // Relations: Связь с последним статусом
    public function latestTaskStatus()
    {
        // Указываем, что последнюю запись ищем по максимальному ID в пивоте
        return $this->hasOne(BlockTaskStatusPivot::class)->latestOfMany('id');

        // ИЛИ, если хочешь по дате создания:
        // return $this->hasOne(TaskStatus::class)->latestOfMany('created_at');
    }

    // Relations: Связь с последним статусом
    public function latestTaskStatusByDate()
    {
        // Указываем, что последнюю запись ищем по максимальному ID в пивоте
        return $this->hasOne(BlockTaskStatusPivot::class)->latestOfMany('created_at');

        // ИЛИ, если хочешь по дате создания:
        // return $this->hasOne(TaskStatus::class)->latestOfMany('created_at');
    }

    // Relations: ОДИН актуальный полноценный статус (пробивая пивот насквозь)
    public function currentStatus(): HasOneThrough
    {
        return $this->hasOneThrough(
            BlockTaskStatus::class,       // Конечная модель, которую хотим получить
            BlockTaskStatusPivot::class,  // Промежуточная таблица (пивот)
            'block_task_id',              // Внешний ключ в промежуточной таблице
            'id',                          // Внешний ключ в конечной таблице
            'id',                          // Локальный ключ в текущей таблице (SewingTask)
            'block_task_status_id'        // Локальный ключ в промежуточной таблице
        )
            // __ Принудительно выбираем поля статуса + нужные поля из пивота с алиасами
            ->select([
                'block_task_statuses.*', // Все поля самого статуса (id, name, color...)

                // __ Вытягиваем поля из таблицы пивота (подставь реальное имя таблицы, если оно отличается)
                'block_task_status_pivot.set_at',
                //'block_task_status_pivot.set_at as pivot_set_at',
                //'block_task_status_pivot.id as pivot_id',
                //'block_task_status_pivot.started_at as pivot_started_at',
                //'block_task_status_pivot.finished_at as pivot_finished_at',
                //'block_task_status_pivot.duration as pivot_duration',
            ])
            ->latestOfMany('id'); // И берем только самый последний по ID пивота!
    }

}
