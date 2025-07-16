<?php

namespace App\Models\Manufacture\Reasons;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at'];
}
