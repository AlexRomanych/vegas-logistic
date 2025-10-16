<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

//use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTaskContext extends Model
{
    protected $guarded = [];

    protected $appends = [
        'average_fabric_length',
        'average_textile_roll_length'
    ];

    protected $casts = [
        'average_fabric_length' => 'float',
        'average_textile_length' => 'float',
        'average_textile_roll_length' => 'float',
        'translate_rate' => 'float',
        'productivity' => 'float'
    ];


    // __ Возвращаем среднюю длину ПС
    public function getAverageFabricLengthAttribute(): float
    {
        return (float)$this->translate_rate === 0.0 ? 0 : (float)$this->average_textile_length / (float)$this->translate_rate / $this->fabric->textile_layers_amount;
    }


    // __ Возвращаем среднюю рулона ткани ПС
    public function getAverageTextileRollLengthAttribute(): float
    {
        return $this->fabric->average_roll_length;
    }

    // relations: Связь со сменным заданием
    public function fabricTask(): BelongsTo
    {
        return $this->belongsTo(FabricTask::class, 'fabric_task_id', 'id', 'fabric_task_id');
        //        return $this->belongsTo(FabricTask::class);
    }

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
