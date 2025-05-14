<?php

namespace App\Models\Manufacture\Cells\Fabric;

use App\Models\User;
use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTaskRoll extends Model
{
    protected $guarded = [];

//    // Relations: Связь со сменным заданием
//    public function fabricTask(): BelongsTo
//    {
//        return $this->belongsTo(FabricTask::class);
//    }


    // Relations: Связь с ПС
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabric::class);
    }


    // Relations: Связь с плановым сменным заданием
    public function fabricTaskContext(): BelongsTo
    {
        return $this->belongsTo(FabricTaskContext::class);
    }


    // Relations: Связь с работягой, ответственного за выпуск рулона
    public function finishBy(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'finish_by', 'id');
    }


    // Relations: Связь с работягой, ответственного за постановку рулона в 1С
    public function registration1CBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registration_1C_by', 'id');
    }


    // Relations: Связь с работягой, ответственного за перемещение рулона на закрой
    public function moveToCutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'move_to_cut_by', 'id');
    }


    // Relations: Связь с работягой, ответственного за принятие рулона на закрой
    public function receiptToCutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receipt_to_cut_by', 'id');
    }


    // Relations: Связь с работягой, от записи которого делались действия с рулоном на производстве
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
