<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelConstruct extends LaravelModel
{
    protected $primaryKey = CODE_1C;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;

    protected $casts = [
        'active' => 'boolean'
    ];


    // Relations: Связь с Элементами спецификации
    public function constructItems(): HasMany
    {
        return $this->hasMany(ModelConstructItem::class, 'construct_code_1c', CODE_1C);
    }

    // Relations: Связь с Моделью
    public function model(): BelongsTo
    {
        return $this->belongsTo(/*\App\Models\Models\*/Model::class);
    }
}
