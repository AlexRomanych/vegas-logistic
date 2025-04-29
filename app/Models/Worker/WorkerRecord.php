<?php

namespace App\Models\Worker;

use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkerRecord extends Model
{

    // Relations: Привязываемся к Worker
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    // Relations: Привязываемся к fabric_tasks_date - таблица с днями стегания
    public function fabricTasksDate(): BelongsToMany
    {
        return $this->belongsToMany(
            FabricTasksDate::class,                // Класс связанной модели
            'worker_record_fabric_task_date',        // Таблица связи
            'worker_record_id',             // Поле, которое содержит ID текущей модели в связанной таблице
            'fabric_tasks_date_id');        // Поле, которое содержит ID связанной модели в связанной таблице
    }
}
