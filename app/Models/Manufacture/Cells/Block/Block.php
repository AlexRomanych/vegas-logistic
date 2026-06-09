<?php

namespace App\Models\Manufacture\Cells\Block;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    protected $guarded = false;

    protected $casts = [
        'active' => 'boolean',
        'width'  => 'integer',
        'length' => 'integer',
        'height' => 'integer',
    ];

    // Relations: Связь с группой (коллекцией) блоков
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'collection', CODE_1C);
    }

}
