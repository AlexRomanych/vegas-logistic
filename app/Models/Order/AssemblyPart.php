<?php

namespace App\Models\Order;

use App\Models\Abstract\Part;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssemblyPart extends Part
{
    protected $fillable = [
        'manufacture_date',
        'index',
        'comment',
        'order_id',
    ];

    protected $hidden = [
        'id',
        'created_at', 'updated_at', 'order_id',
    ];

    protected $with = [
        'lines'
    ];



    public function lines(): HasMany
    {
        return $this->hasMany(Line::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
