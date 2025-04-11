<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricTasksDate extends Model
{
    protected $guarded = ['id'];

    public function getTasksDateAttribute($value): string
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    // Relations Связь со сменными заданиями на СМ
    public function fabricTasks(): HasMany
    {
        return $this->hasMany(FabricTask::class);
    }
}
