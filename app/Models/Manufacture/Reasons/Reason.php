<?php

namespace App\Models\Manufacture\Reasons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reason extends Model
{
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at'];

    // Relations: Связь с Категорией причин
    public function reasonCategory(): BelongsTo
    {
        return $this->belongsTo(ReasonCategory::class);
    }
}
