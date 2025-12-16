<?php

namespace App\Models\Materials;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $primaryKey = CODE_1C;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;

    protected $casts = [
        'is_shown' => 'boolean',
        'is_deleted' => 'boolean',
        'apply_alt_unit' => 'boolean',
        'active' => 'boolean',
        'alt_multiplier' => 'float',
    ];


}
