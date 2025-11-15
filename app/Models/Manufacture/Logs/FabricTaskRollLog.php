<?php

namespace App\Models\Manufacture\Logs;

use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\User;
use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTaskRollLog extends Model
{
    protected $guarded = false;

    // Relations: Связь с Физическим рулоном
    public function fabricTaskRoll(): BelongsTo
    {
        return $this->belongsTo(FabricTaskRoll::class);
    }


    // Relations: Связь с User (учетной записью)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    // Relations: Связь с Ответственным работником
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'id', 'workers');
    }
}
