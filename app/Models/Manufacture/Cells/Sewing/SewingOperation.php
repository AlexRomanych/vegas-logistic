<?php

namespace App\Models\Manufacture\Cells\Sewing;

use Illuminate\Database\Eloquent\Model;

class SewingOperation extends Model
{

    // __ Константы Типа Операции
    public const DYNAMIC_TYPE = 'dynamic';
    public const STATIC_TYPE = 'static';



    protected $guarded = [];

    protected $casts = [
        'time' => 'integer'
    ];

}
