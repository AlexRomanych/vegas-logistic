<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricMachine extends Model
{
    public $guarded = [];
    public function fabrics(): HasMany
    {
        return $this->hasMany(Fabric::class);
    }
}
