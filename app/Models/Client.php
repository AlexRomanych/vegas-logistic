<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Client extends Model
{
    protected $fillable = [
        'id',
        'name', 'add_name', 'short_name', 'description',
        'region', 'country', 'address',  'longitude', 'latitude',
        'active', 'manager_id'
    ];

    protected $hidden = [
        // 'id', 'manager_id', 'order_id',
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'longitude' => 'float',
        'latitude' => 'float',
    ];

//    protected $with = [
//      'manager'
//    ];

    // Attract Связь с менеджером
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
}
