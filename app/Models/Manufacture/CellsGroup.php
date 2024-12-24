<?php

namespace App\Models\Manufacture;

use App\Models\Abstract\CellGroup;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CellsGroup extends CellGroup
{


    protected $with = ['cellItem'];

    public function cellItem(): HasMany
    {
        return $this->hasMany(CellItem::class);
    }
}
