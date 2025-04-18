<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
//use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTaskContext extends Model
{
    protected $guarded = [];

    // attract Связь со сменным заданием
//    public function fabricTask(): BelongsTo
//    {
//        return $this->belongsTo(FabricTask::class, 'fabric_task_id', 'id', 'fabric_task_id');
////        return $this->belongsTo(FabricTask::class);
//    }

// relations: Связь с ПС
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabric::class);
    }

// relations: Связь с физическими рулонами
    public function fabricTaskRolls(): HasMany
    {
        return $this->hasMany(FabricTaskRoll::class);
    }
}
