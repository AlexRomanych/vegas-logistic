<?php

namespace App\Models\BusinessProcesses;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BusinessProcessNodesConnection extends Pivot
{
    protected $table = 'business_process_nodes_connections';


    // Relations: Связь с бизнес-процессами
    public function businessProcesses(): BelongsTo
    {
        return $this->belongsTo(BusinessProcess::class);
    }



}
