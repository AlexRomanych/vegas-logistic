<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockTaskLine extends Model
{
    protected $guarded = false;

    protected $casts = [
        'order_line_ids' => 'json',
        'time'           => 'float',
        'square'         => 'float',
        'productivity'   => 'float',
        'line'           => 'string',

    ];


    // Relations: Связь с Моделью Блока
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_code_1c', CODE_1C);
    }
}
