<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricTask extends Model
{
    protected $guarded = [];
//    protected $hidden = ['created_at', 'updated_at'];

//    protected $with = ['fabricTaskRolls'];
//    protected $with = ['fabricTaskRolls', 'fabricTaskContexts'];

    // attract: Определяем аттрибуты
    /**
     * @param $value
     * @return string
     */
    public function getTaskDateAttribute($value): string
    {
        return Carbon::parse($value)->format('Y-m-d');
    }


    // Relations: Связь со теоретическими рулонами в сменном задании (выставленным ОПП)
    public function fabricTaskContexts(): HasMany
    {
        return $this->hasMany(FabricTaskContext::class);
    }


    // Relations: Связь со стегальными машинами
    public function fabricMachine(): BelongsTo
    {
        return $this->belongsTo(FabricMachine::class);
    }


    // Relations: Связь с группой сменных заданий
    public function fabricTasksDate(): BelongsTo
    {
        return $this->belongsTo(FabricTasksDate::class);
    }


    // relations: Связь с рулонами в сменном задании
//    public function fabricTaskRolls(): HasMany
//    {
//        return $this->hasMany(FabricTaskRoll::class);
//    }

}
