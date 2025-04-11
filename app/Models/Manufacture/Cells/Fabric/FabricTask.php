<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricTask extends Model
{
    protected $guarded = [];
//    protected $hidden = ['created_at', 'updated_at'];

//    protected $with = ['fabricTaskRolls'];
//    protected $with = ['fabricTaskRolls', 'fabricTaskContexts'];

    // attract: Определяем аттрибуты
    /**
     * @param $value
     * @return string
     */
    public function getTaskDateAttribute($value): string
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    // attract: Связь со теоретическими рулонами в сменном задании (выставленным ОПП)
    public function fabricTaskContexts(): HasMany
    {
        return $this->hasMany(FabricTaskContext::class);
    }

    // attract: Связь с рулонами в сменном задании
    public function fabricTaskRolls(): HasMany
    {
        return $this->hasMany(FabricTaskRoll::class);
    }

}
