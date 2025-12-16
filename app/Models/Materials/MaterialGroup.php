<?php

namespace App\Models\Materials;

use Illuminate\Database\Eloquent\Model;

class MaterialGroup extends Model
{
    protected $primaryKey = CODE_1C;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;

    protected $casts = [
        'active'         => 'boolean',
        'is_shown'       => 'boolean',
        'is_deleted'     => 'boolean',
        'is_collapsed'   => 'boolean',
        'apply_alt_unit' => 'boolean',
    ];

}
