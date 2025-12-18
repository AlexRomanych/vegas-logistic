<?php

namespace App\Models\Materials;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    protected $primaryKey = CODE_1C;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;

    protected $casts = [
        'is_shown'       => 'boolean',
        'is_deleted'     => 'boolean',
        'apply_alt_unit' => 'boolean',
        'active'         => 'boolean',
        'is_collapsed'   => 'boolean',
        'is_checked'     => 'boolean',
        'alt_multiplier' => 'float',
    ];

    protected $appends = [
        'is_group',
        'is_category',
        'is_material',
    ];

    // __ Проверка на группу
    protected function isGroup(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (
                is_null($attributes['material_group_code_1c'])
                && is_null($attributes['material_category_code_1c'])
            ), //->shouldCache(), // ⬅️ Результат будет вычислен только один раз,
        );
    }

    // __ Проверка на Категорию
    protected function isCategory(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (
                !is_null($attributes['material_group_code_1c'])
                && is_null($attributes['material_category_code_1c'])
            ),
        );
    }

    // __ Проверка на Материал
    protected function isMaterial(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (
                is_null($attributes['material_group_code_1c'])
                && !is_null($attributes['material_category_code_1c'])
            ),
        );
    }


    // Relations: === СВЯЗИ ДЛЯ ДВИЖЕНИЯ ВВЕРХ (К РОДИТЕЛЯМ) ===

    // Relations: Связь с Категорией
    public function category(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_category_code_1c', CODE_1C);
    }

    // Relations: Связь с Группой
    public function group(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_group_code_1c', CODE_1C);
    }


    // Relations: === СВЯЗИ ДЛЯ ДВИЖЕНИЯ ВНИЗ (К ПОТОМКАМ) ===

    // Relations: Связь с Категорией. Если текущий объект — Группа, получаем все её Категории.
    public function categories(): HasMany
    {
        return $this->hasMany(Material::class, 'material_group_code_1c', CODE_1C);
    }

    // Relations: Связь с Группой. Если текущий объект — Категория, получаем все её Материалы.
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'material_category_code_1c', CODE_1C);
    }


}
