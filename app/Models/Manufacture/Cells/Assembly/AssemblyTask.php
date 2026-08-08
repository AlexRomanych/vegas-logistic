<?php

namespace App\Models\Manufacture\Cells\Assembly;

use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

use function PHPUnit\Framework\isNull;


/**
 * @method static Builder|AssemblyTask query()
 * @method Builder|AssemblyTask byStatus(mixed $data)
 * @method Builder|AssemblyTask whereDayAt(mixed $data)
 * @method Builder|AssemblyTask sectors(mixed $sectors, mixed $relations)
 * @method Builder|AssemblyTask sectorCoconut(mixed $relations)
 */
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
    public const ASSEMBLY_TASK_SECTOR_LAMIT = 'lamit';              // __ Ламит
    public const ASSEMBLY_TASK_SECTOR_TABLE = 'table';              // __ Стол

    public const ASSEMBLY_TASK_SECTORS = [
        self::ASSEMBLY_TASK_SECTOR_FOAM_SIDE,
        self::ASSEMBLY_TASK_SECTOR_FOAM_LAYER,
        self::ASSEMBLY_TASK_SECTOR_LATEX,
        self::ASSEMBLY_TASK_SECTOR_LAYER,
        self::ASSEMBLY_TASK_SECTOR_COCONUT,
    ];

    public const FIELD_UNIVERSAL = 'universal';
    public const FIELD_AUTO = 'auto';
    public const FIELD_SOLID_HARD = 'solid_hard';
    public const FIELD_SOLID_LITE = 'solid_lite';
    public const FIELD_UNDEFINED = 'undefined';


    // --- -------------------------------

    // --- -------------------------------
    // --- ---------- Scopes -------------
    // --- -------------------------------

    // Scopes
    // Scopes __ По задумке:
    // Scopes __ 1. Если передана строка (название участка), то получаем СЗ вместе с этим участком
    // Scopes __ 2. Если передан массив строк (название участков), то получаем СЗ вместе с этими участками
    // Scopes __ 3. Если передан пустой массив [], то получаем СЗ без этих участков
    // Scopes __ 4. Если не передано ничего null, то получаем СЗ со всеми участками
    public function scopeSectors(
        $query,
        array|string|null $sectors = null,
        array|string|null $relations = null
    ): Builder {
        // __ Приводим строку к массиву для удобства
        if (is_string($relations)) {
            $relations = [$relations];
        }

        // __ Подгружаем lines в любом раскладе
        $query->with(['lines']);

        // __ Подгружаем отношения к Контексту Заявки
        if (!is_null($relations)) {
            $query->with($relations);
        }

        // __ Возвращаем вообще все, грузим ВСЕ связи без ограничений
        if (is_null($sectors)) {
            return $query->with([/*'lines', */ 'lines.sectors']);
        }

        // __ Приводим строку к массиву для удобства
        if (is_string($sectors)) {
            $sectors = [$sectors];
        }

        // __ Если $sectors пустой массив - не грузим участки
        if (empty($sectors)) {
            /** @var Builder $query */
            return $query;
        }

        // __ Если передан непустой массив $sectors:

        // __ А) Фильтруем сами AssemblyTask (оставляем только задачи, имеющие нужные участки)
        $query->whereHas('lines.sectors', function ($q) use ($sectors) {
            $q->whereIn('sector', $sectors);
        });

        // __ Б) Жадно загружаем (with) ТОЛЬКО те lines и sectors, которые подходят под фильтр
        return $query->with([
            'lines' => function ($qLine) use ($sectors) {
                // Оставляем только строки, связанные с нужными участками
                $qLine->whereHas('sectors', function ($q) use ($sectors) {
                    $q->whereIn('sector', $sectors);
                })
                    // И внутри строк загружаем ТОЛЬКО целевые участки
                    ->with([
                        'sectors' => function ($qSector) use ($sectors) {
                            $qSector->whereIn('sector', $sectors);
                        }
                    ]);
            }
        ]);
    }

    // Scopes Фильтруем по Кокосу
    public function scopeSectorCoconut($query, array|string|null $relations = null): Builder
    {
        return $this->scopeSectors($query, self::ASSEMBLY_TASK_SECTOR_COCONUT, $relations);
    }


    // Scopes Фильтр по статусу
    public function scopeByStatus($query, array|string|int $statusIds = null)
    {
        if (empty($statusIds)) {
            return $query;
        }

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

    // Scopes Фильтр по дате
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
                AssemblyTaskStatus::class,           // Класс, с которым связываемся
                AssemblyTaskStatusPivot::TABLE,      // Промежуточная Таблица, связывающая классы
                'assembly_task_id',                  // Ключ в промежуточной таблице, связывающий с текущим классом
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
            'id',                            // Внешний ключ в конечной таблице
            'id',                            // Локальный ключ в текущей таблице (SewingTask)
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
