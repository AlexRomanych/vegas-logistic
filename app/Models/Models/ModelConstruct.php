<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelConstruct extends LaravelModel
{
    public const PANEL_NAME = 'panel';  // __ Верхняя и нижняя крышки одинаковы
    public const PANEL_UP_NAME = 'panel_up';
    public const PANEL_DOWN_NAME = 'panel_down';
    public const SIDE_NAME = 'side';

    public const DETAIL_CONSTRUCT_PANEL_NAME = 'Крышка';
    public const DETAIL_CONSTRUCT_SIDE_NAME = 'Боковина';


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
        return $this
            ->hasMany(ModelConstructItem::class, 'construct_code_1c', CODE_1C)
            ->orderBy('position', 'asc'); // 'asc' — по возрастанию (1, 2, 3...), 'desc' — по убыванию;
    }

    // Relations: Связь с Моделью
    public function model(): BelongsTo
    {
        return $this->belongsTo(/*\App\Models\Models\*/Model::class);
    }
}
