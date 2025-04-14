<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTaskRoll extends Model
{
    protected $guarded = [];

    // attract: Связь со сменным заданием
    public function fabricTask(): BelongsTo
    {
        return $this->belongsTo(FabricTask::class);
    }


    // attract: Связь с плановым сменным заданием
    public function fabricTaskContext(): BelongsTo
    {
        return $this->belongsTo(FabricTaskContext::class);
    }
}
