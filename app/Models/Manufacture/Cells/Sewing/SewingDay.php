<?php

namespace App\Models\Manufacture\Cells\Sewing;

use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SewingDay extends Model
{
    protected $guarded = false;

    protected $casts = [
        'change'    => 'integer',
        // 'action_at' => 'datetime',
    ];

    // protected function casts(): array
    // {
    //     return [
    //         'action_at' => 'date:Y-m-d',
    //     ];
    // }

    // __ Сериализация даты для всех дат
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
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
