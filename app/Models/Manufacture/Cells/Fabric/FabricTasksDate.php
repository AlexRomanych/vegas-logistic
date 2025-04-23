<?php

namespace App\Models\Manufacture\Cells\Fabric;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    // Relations Связь с текущим пользователем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relations Связь со сменой сотрудников на СМ
    public function team(): BelongsTo
    {
        return $this->belongsTo(FabricTeam::class, 'fabric_team_id', 'id', 'fabric_teams');
//        return $this->belongsTo(FabricTeam::class);   // attract: Не работает, надо разобраться почему
    }

}
