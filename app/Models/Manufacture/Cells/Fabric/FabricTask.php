<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricTask extends Model
{
    protected $guarded = [];
//    protected $hidden = ['created_at', 'updated_at'];

    protected $with = ['fabricTaskRolls'];

    // Связь с рулонами в сменном задании
    public function fabricTaskRolls(): HasMany
    {
        return $this->hasMany(FabricTaskRoll::class);
    }

}
