<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricOrder extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    protected $with = ['fabricExpense'];

    // Attract Связь со списком расходов
    public function fabricExpense(): HasMany
    {
        return $this->hasMany(FabricExpense::class);
    }
}
