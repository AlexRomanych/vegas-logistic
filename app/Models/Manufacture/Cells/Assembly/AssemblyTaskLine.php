<?php

namespace App\Models\Manufacture\Cells\Assembly;

use App\Models\Order\OrderLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 */
class AssemblyTaskLine extends Model
{

    protected $guarded = false;

    protected $casts = [
        'time_labor'       => 'array',
        'phantom_json'     => 'array',
        'false_history'    => 'array',
    ];

    // Relations: Связь с Контекстом Заявки (OrderLine)
    public function orderLine(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class);
    }


    // Relations: Связь с Контекстом Участка
    public function sectors(): HasMany
    {
        return $this->hasMany(AssemblyTaskLineSector::class, 'assembly_task_line_id');
    }

    // Relations:: Связь с Контекстом Участка Латекса
    public function sectorLatex(): HasMany
    {
        return $this
            ->hasMany(AssemblyTaskLineSector::class)
            ->where('sector', AssemblyTask::ASSEMBLY_TASK_SECTOR_LATEX);
    }

    // Relations:: Связь с Контекстом Участка Кокоса
    public function sectorCoconut(): HasMany
    {
        return $this
            ->hasMany(AssemblyTaskLineSector::class)
            ->where('sector', AssemblyTask::ASSEMBLY_TASK_SECTOR_COCONUT);
    }
}
