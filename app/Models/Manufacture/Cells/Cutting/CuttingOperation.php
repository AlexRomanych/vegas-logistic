<?php

namespace App\Models\Manufacture\Cells\Cutting;

use Illuminate\Database\Eloquent\Model;

class CuttingOperation extends Model
{

    // __ Константы Типа Операции
    public const DYNAMIC_TYPE = 'dynamic';
    public const STATIC_TYPE = 'static';

    // __ Константы Детали Операции
    public const DETAIL_PANEL = 'panel';
    public const DETAIL_SIDE = 'side';


    protected $guarded = [];

    protected $casts = [
        'time' => 'double'
    ];

}
