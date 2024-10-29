<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Procedure extends Model
{
    protected $primaryKey = 'code1C';
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code1C',
        'name', 'text',
        'object_code1C', 'object_type',
    ];


}
