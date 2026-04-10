<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelCollection extends LaravelModel
{
    protected $primaryKey = CODE_1C;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;

    // protected $fillable = [
    //     'code1C', 'name', 'active'
    // ];

    protected $casts = [
        'active' => 'boolean'
    ];


    // Relations: Связь с моделями
    public function models(): HasMany
    {
        return $this->hasMany(Model::class, 'model_collection_code_1c', CODE_1C );
    }
}
