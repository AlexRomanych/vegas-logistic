<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

class ModelConstruct extends Model
{
    protected $primaryKey = CODE_1C;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;

    protected $casts = [
        'active' => 'boolean'
    ];
}
