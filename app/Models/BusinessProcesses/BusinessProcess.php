<?php

namespace App\Models\BusinessProcesses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProcess extends Model
{
    protected $guarded = false;

    protected $hidden = ['created_at', 'updated_at'];


    // Relations: Связь с Референсным Бизнес-узлом (от которого пляшут все вычисления, например по дате)
    public function referenceNode(): BelongsTo
    {
        return $this->belongsTo(BusinessProcessNode::class, 'reference_node_id');
    }

    // Relations: Связь со Стартовым Бизнес-узлом (начало Бизнес-процесса)
    public function startNode(): BelongsTo
    {
        return $this->belongsTo(BusinessProcessNode::class, 'start_node_id');
    }

    // Relations: Связь со Стартовым Бизнес-узлом (начало Бизнес-процесса)
    public function finishNode(): BelongsTo
    {
        return $this->belongsTo(BusinessProcessNode::class, 'finish_node_id');
    }
}
