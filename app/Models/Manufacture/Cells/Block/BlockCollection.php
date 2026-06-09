<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockCollection extends Model
{
    protected $guarded = false;

    protected $casts = [
        'active'   => 'boolean',
        'priority' => 'integer',
        'line'     => 'integer',
    ];

    // Relations: Связь с блоками
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'collection', CODE_1C);
    }
}
