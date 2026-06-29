<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Model;

class BlockTaskLine extends Model
{
    protected $guarded = false;

    protected $casts = [
        'order_line_ids' => 'json',
    ];
}
