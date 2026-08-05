<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

final class ModelManufactureGroup extends Model
{
    protected $guarded = false;

    protected $casts = [
        'group_number' => 'integer',
        'active'       => 'boolean',
    ];
}
