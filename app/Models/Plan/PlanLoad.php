<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class PlanLoad extends Model
{
    protected $guarded = false;

    protected $casts = [
        'amounts' => 'array',
    ];
}
