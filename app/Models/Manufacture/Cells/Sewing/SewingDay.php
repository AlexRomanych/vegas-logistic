<?php

namespace App\Models\Manufacture\Cells\Sewing;

use App\Models\Worker\Worker;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SewingDay extends Model
{
    protected $guarded = false;

    protected $casts = [
        'change' => 'integer',
        // 'action_at' => 'datetime',
    ];

    // protected function casts(): array
    // {
    //     return [
    //         'action_at' => 'date:Y-m-d',
    //     ];
    // }


    /**
     * __ Поиск или создание записи по дате и смене
     * @param $date
     * @param  int  $change
     * @return SewingDay
     */
    public static function findOrCreateByDate($date, int $change = 1): self
    {
        return self::query()
            ->with(['workers', 'responsible'])
            ->firstOrCreate([
                'action_at'     => Carbon::parse($date)->format(RETURN_DATE_TIME_FORMAT),
                'action_at_str' => $date,
                'change'        => $change,
            ]);
    }


    // __ Сериализация даты для всех дат
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    // __ Поиск по датам
    public function scopeByDates($query, $dates = null)
    {
        // __ Если массив пустой, возвращаем запрос без изменений
        if (empty($dates)) {
            return $query;
        }

        // __Приводим к массиву на случай, если пришла одна строка
        $dates = is_array($dates) ? $dates : [$dates];

        // __ Если в базе action_at — это DATE (YYYY-MM-DD), используем whereIn.
        // __ Если в базе action_at — это DATETIME/TIMESTAMP, используем whereIn
        // __ с приведением типа через подзапрос или raw, чтобы игнорировать время.

        return $query->whereIn(DB::raw('DATE(action_at)'), $dates);
    }


    // Relations: Связь с Ответственным лицом
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'responsible_id');
    }


    // Relations: Связь с Операциями
    public function workers(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Worker::class,                     // Класс, с которым связываемся
                SewingDayWorkerPivot::TABLE,       // Промежуточная Таблица, связывающая классы
                'sewing_day_id',                   // Ключ в промежуточной таблице, связывающий с текущим классом
                'worker_id'           // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            )
            ->using(SewingDayWorkerPivot::class)
            ->withPivot(['id', 'working_time']);
    }

}
