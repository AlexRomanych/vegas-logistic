<?php

namespace App\Models\Manufacture\Cells\Block;

use App\Models\Manufacture\Documents\BlockDesignDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockCollection extends Model
{
    public const LINE_0 = 0;
    public const LINE_1 = 1;
    public const LINE_2 = 2;

    public const UNIT = '';
    public const UNIT_PIC = 'шт.';
    public const UNIT_METERS = 'м.п.';


    protected $guarded = false;

    protected $casts = [
        'active'       => 'boolean',
        'priority'     => 'integer',
        'line'         => 'integer',
        'productivity' => 'float',
        'height'       => 'integer',
        'length'       => 'integer',
        'own'          => 'boolean',
    ];


    // Relations: Связь с блоками
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'collection', CODE_1C);
    }

    // Relations: Связь с КДБ
    public function kdbDoc()
    {
        return $this->hasOne(BlockDesignDocument::class, 'kdb', 'kdb');
    }

}
