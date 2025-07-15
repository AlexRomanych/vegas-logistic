<?php

namespace App\Models\Manufacture;

use App\Models\Abstract\CellGroup;
use App\Models\Manufacture\Reasons\ReasonCategory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CellsGroup extends CellGroup
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

//    protected $with = ['cellItems'];

    // Relations: Связь с производственной ячейкой
    public function cellItems(): HasMany
    {
        return $this->hasMany(CellItem::class);
    }


    // Relations: Связь с Категорией причин
    public function reasonCategories(): HasMany
    {
        return $this->hasMany(ReasonCategory::class);

    }


}
