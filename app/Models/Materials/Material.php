<?php

namespace App\Models\Materials;

use App\Models\Order\OrderLine;
use App\Models\Order\OrderLineMaterialPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;


/**
 * @method static Builder|Material assembly()
 * @method static Builder|Material group()
 */
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
        'properties'     => 'array',
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

    // Scopes: Только Группы
    public function scopeGroup(Builder $query): Builder
    {
        return $query
            ->whereNull('material_group_code_1c')
            ->whereNull('material_category_code_1c');
    }


    // Scopes: Возвращаем структуру материалов Сборки ("С_...")
    public function scopeAssembly(Builder $query, array|string|null $code1c = null): Builder
    {
        $codes = [];
        if (is_string($code1c)) {
            $codes = [$code1c];
        } elseif (is_array($code1c)) {
            $codes = $code1c;
        }

        return $query
            ->group()
            ->whereLike('name', 'С\_%')
            ->when(!empty($codes), function (Builder $q) use ($codes) {
                $q->whereIn(CODE_1C, $codes); // Если указан ключ/константа поля, например CODE_1C или 'code_1c'
            })
            ->with(['categories', 'categories.materials']);
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
        return $this
            ->hasMany(Material::class, 'material_group_code_1c', CODE_1C)
            ->whereNull('material_category_code_1c');
    }

    // Relations: Связь с Группой. Если текущий объект — Категория, получаем все её Материалы.
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'material_category_code_1c', CODE_1C);
    }


    // В модели Material
    //public function childrenCategories(): HasMany
    //{
    //    return $this->categories()->with('childrenCategories');
    //}


    // Relations: Связь со сторокой Заказа, для которой просчитан расход
    public function orderLines(): BelongsToMany
    {
        return $this->belongsToMany(
            OrderLine::class,
            'order_line_material_pivot',
            'material_code_1c',
            'order_line_id'
        )->using(OrderLineMaterialPivot::class);
    }

}
