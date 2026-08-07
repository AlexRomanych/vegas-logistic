<?php

namespace App\Models\Manufacture\Cells\Assembly;

use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class AssemblyTask extends Model
{
    protected $guarded = false;
    protected $casts = [
        'action_at' => 'datetime',
    ];


    // --- Константы
    public const ASSEMBLY_TASK_SECTOR_FOAM_SIDE = 'foam_side';      // __ Борта
    public const ASSEMBLY_TASK_SECTOR_FOAM_LAYER = 'foam_layer';    // __ Настилы
    public const ASSEMBLY_TASK_SECTOR_LATEX = 'latex';              // __ Латекс
    public const ASSEMBLY_TASK_SECTOR_LAYER = 'layer';              // __ Тонкий настил
    public const ASSEMBLY_TASK_SECTOR_COCONUT = 'coconut';          // __ Кокос

    public const ASSEMBLY_TASK_SECTORS = [
        self::ASSEMBLY_TASK_SECTOR_FOAM_SIDE,
        self::ASSEMBLY_TASK_SECTOR_FOAM_LAYER,
        self::ASSEMBLY_TASK_SECTOR_LATEX,
        self::ASSEMBLY_TASK_SECTOR_LAYER,
        self::ASSEMBLY_TASK_SECTOR_COCONUT,
    ];

    // --- Поля
    //public const FIELD_TABLE_1 = 'table_1';
    //public const FIELD_TABLE_2 = 'table_2';
    //public const FIELD_TABLE_3 = 'table_3';
    //public const FIELD_AVERAGE = 'average';


    public const FIELD_UNIVERSAL = 'universal';
    public const FIELD_AUTO = 'auto';
    public const FIELD_SOLID_HARD = 'solid_hard';
    public const FIELD_SOLID_LITE = 'solid_lite';
    public const FIELD_UNDEFINED = 'undefined';



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
            $q->whereIn('assembly_task_status_id', $statusIds);
        });


        // return $query->whereHas('statuses', function ($q) use ($statusIds) {
        //     // Мы фильтруем прямо по полю status_id в промежуточной таблице
        //     // Это быстрее, так как не нужно джойнить таблицу statuses
        //     $q->whereIn('assembly_task_status_id', $statusIds);
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
    public function assemblyLines(): HasMany
    {
        return $this
            ->hasMany(AssemblyTaskLine::class, 'assembly_task_id')
            ->orderBy('position');
    }

    public function lines(): HasMany
    {
        return $this
            ->hasMany(AssemblyTaskLine::class, 'assembly_task_id')
            ->orderBy('position');
    }


    // Relations: Связь со Статусами
    public function statuses(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                AssemblyTaskStatus::class,         // Класс, с которым связываемся
                AssemblyTaskStatusPivot::TABLE,      // Промежуточная Таблица, связывающая классы
                'assembly_task_id',                // Ключ в промежуточной таблице, связывающий с текущим классом
                'assembly_task_status_id' // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            )
            ->using(AssemblyTaskStatusPivot::class)
            ->withPivot(['id', 'set_at', 'started_at', 'finished_at', 'duration', 'created_at', 'updated_at']);
    }


    // Relations: Связь с последним статусом
    public function latestTaskStatus()
    {
        // Указываем, что последнюю запись ищем по максимальному ID в пивоте
        return $this->hasOne(AssemblyTaskStatusPivot::class)->latestOfMany('id');

        // ИЛИ, если хочешь по дате создания:
        // return $this->hasOne(TaskStatus::class)->latestOfMany('created_at');
    }

    // Relations: Связь с последним статусом
    public function latestTaskStatusByDate()
    {
        // Указываем, что последнюю запись ищем по максимальному ID в пивоте
        return $this->hasOne(AssemblyTaskStatusPivot::class)->latestOfMany('created_at');

        // ИЛИ, если хочешь по дате создания:
        // return $this->hasOne(TaskStatus::class)->latestOfMany('created_at');
    }

    // Relations: ОДИН актуальный полноценный статус (пробивая пивот насквозь)
    public function currentStatus(): HasOneThrough
    {
        return $this->hasOneThrough(
            AssemblyTaskStatus::class,       // Конечная модель, которую хотим получить
            AssemblyTaskStatusPivot::class,  // Промежуточная таблица (пивот)
            'assembly_task_id',              // Внешний ключ в промежуточной таблице
            'id',                          // Внешний ключ в конечной таблице
            'id',                          // Локальный ключ в текущей таблице (SewingTask)
            'assembly_task_status_id'        // Локальный ключ в промежуточной таблице
        )
            // __ Принудительно выбираем поля статуса + нужные поля из пивота с алиасами
            ->select([
                'assembly_task_statuses.*', // Все поля самого статуса (id, name, color...)

                // __ Вытягиваем поля из таблицы пивота (подставь реальное имя таблицы, если оно отличается)
                'assembly_task_status_pivot.set_at',
                //'assembly_task_status_pivot.set_at as pivot_set_at',
                //'assembly_task_status_pivot.id as pivot_id',
                //'assembly_task_status_pivot.started_at as pivot_started_at',
                //'assembly_task_status_pivot.finished_at as pivot_finished_at',
                //'assembly_task_status_pivot.duration as pivot_duration',
            ])
            ->latestOfMany('id'); // И берем только самый последний по ID пивота!
    }

}
