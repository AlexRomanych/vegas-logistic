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

    protected $with = ['fabricMainMachine'];


//    protected $attributes = [
//
//    ];

    //h2 fabric (ПС) -----------------------------------------------------------------------------------------
    public function fabric(): HasMany
    {
        return $this->hasMany(Fabric::class);
    }

    //h2 fabric_main_machine (основная стегальная машина) -----------------------------------------------------
    public function fabricMainMachine(): BelongsTo
    {
        return $this->belongsTo(FabricMachine::class, 'fabric_machine_id', 'id', 'fabric_machines');
    }
}

