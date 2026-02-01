<?php

namespace App\Models\Manufacture\Cells\Sewing;

use Illuminate\Database\Eloquent\Model;

class SewingOperation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'time' => 'integer'
    ];

}
