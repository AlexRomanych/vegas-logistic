<?php

namespace App\Models\Manufacture\Reasons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReasonCategory extends Model
{
    protected $guarded = false;

    protected $hidden = ['created_at', 'updated_at'];


    // Relations: Связь с причинами
    public function reasons(): HasMany
    {
        return $this->hasMany(Reason::class);
    }

}
