<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

final class ModelManufactureType extends Model
{
    protected $primaryKey = CODE_1C;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;

    protected $casts = [
        'active' => 'boolean'
    ];



}
