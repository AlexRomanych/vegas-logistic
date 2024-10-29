<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Collection extends Model
{
    protected $primaryKey = 'code1C';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code1C', 'name', 'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function models(): HasMany
    {
        return $this->hasMany(\App\Models\Model::class, 'collection_id', 'code1C' );
    }
}
