<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTaskContext extends Model
{
    protected $guarded = [];

    // attract Связь со сменным заданием
//    public function fabricTask(): BelongsTo
//    {
//        return $this->belongsTo(FabricTask::class, 'fabric_task_id', 'id', 'fabric_task_id');
////        return $this->belongsTo(FabricTask::class);
//    }
}
