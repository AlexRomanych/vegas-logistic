<?php

namespace App\Models\Manufacture;

use App\Models\Abstract\CellGroup;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CellsGroup extends CellGroup
{
    protected $guarded = [];

    protected $with = ['cellItems'];

    public function cellItems(): HasMany
    {
        return $this->hasMany(CellItem::class);
    }
}
