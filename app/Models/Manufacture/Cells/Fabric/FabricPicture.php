<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricPicture extends Model
{

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'stitch_length' => 'float',
    ];

    protected $with = [
        'fabricMainMachine',
        'fabricAltMachine_1',
        'fabricAltMachine_2',
        'fabricAltMachine_3',
        'fabricMainMachineSchema',
        'fabricAltMachineSchema_1',
        'fabricAltMachineSchema_2',
        'fabricAltMachineSchema_3',
    ];


//    protected $attributes = [
//
//    ];

    //Relations: fabric (ПС) -----------------------------------------------------------------------------------------
    public function fabric(): HasMany
    {
        return $this->hasMany(Fabric::class);
    }


    //Relations: fabric_main_machine (основная стегальная машина) -----------------------------------------------------
    public function fabricMainMachine(): BelongsTo
    {
        return $this->belongsTo(FabricMachine::class, 'fabric_machine_id', 'id', 'fabric_machines');
    }


    //Relations: fabric_alt_machine_1 (альтернативная стегальная машина 1) --------------------------------------------
    public function fabricAltMachine_1(): BelongsTo
    {
        return $this->belongsTo(FabricMachine::class, 'alt_machine_1_id', 'id', 'fabric_machines');
    }


    //Relations: fabric_alt_machine_2 (альтернативная стегальная машина 2) --------------------------------------------
    public function fabricAltMachine_2(): BelongsTo
    {
        return $this->belongsTo(FabricMachine::class, 'alt_machine_2_id', 'id', 'fabric_machines');
    }


    //Relations: fabric_alt_machine_3 (альтернативная стегальная машина 3) --------------------------------------------
    public function fabricAltMachine_3(): BelongsTo
    {
        return $this->belongsTo(FabricMachine::class, 'alt_machine_3_id', 'id', 'fabric_machines');
    }


    //Relations: fabric_picture_schema_id схема игл на основной СМ) ---------------------------------------------------
    public function fabricMainMachineSchema(): BelongsTo
    {
        return $this->belongsTo(FabricPictureSchema::class, 'fabric_picture_schema_id', 'id', 'fabric_picture_schemas');
    }


    //Relations: fabric_picture_schema_id схема игл на основной СМ) ---------------------------------------------------
    public function fabricAltMachineSchema_1(): BelongsTo
    {
        return $this->belongsTo(FabricPictureSchema::class, 'alt_machine_1_schema_id', 'id', 'fabric_picture_schemas');
    }


    //Relations: fabric_picture_schema_id схема игл на основной СМ) ---------------------------------------------------
    public function fabricAltMachineSchema_2(): BelongsTo
    {
        return $this->belongsTo(FabricPictureSchema::class, 'alt_machine_2_schema_id', 'id', 'fabric_picture_schemas');
    }


    //Relations: fabric_picture_schema_id схема игл на основной СМ) ---------------------------------------------------
    public function fabricAltMachineSchema_3(): BelongsTo
    {
        return $this->belongsTo(FabricPictureSchema::class, 'alt_machine_3_schema_id', 'id', 'fabric_picture_schemas');
    }

}

