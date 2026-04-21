<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    protected $guarded = false;

    protected $casts = [
        'context' => 'array',
    ];
}
