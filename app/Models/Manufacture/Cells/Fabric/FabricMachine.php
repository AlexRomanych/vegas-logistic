<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricMachine extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    //Relations: fabricPicture (рисунки ПС) - ---------------------------------------------------------------------
    public function fabricPictures(): HasMany
    {
        return $this->hasMany(FabricPicture::class);
    }
}
