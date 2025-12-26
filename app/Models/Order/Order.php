<?php

namespace App\Models\Order;

use App\Enums\ElementTypes;
use App\Models\Client;

//use App\Models\Shared\Period;
//use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = false;

    protected $casts = [
        'amounts' => 'json',
        // 'amounts' => 'array',
    ];

    protected $appends = ['elements_type_render'];

    // __ Преобразование типа Заявки в русский язык (mattresses+covers --> матрасы+чехлы)
    protected function elementsTypeRender(): Attribute
    {
        return Attribute::make(
            get: function(mixed $value, array $attributes) {
                $search = [
                    ElementTypes::UNDEFINED->value,
                    ElementTypes::MATTRESSES->value,
                    ElementTypes::COVERS->value,
                    ElementTypes::ACCESSORIES->value,
                    ElementTypes::MIXED->value,
                    ElementTypes::AVERAGE->value,
                ];
                $replace = [
                    'не известно',
                    'матрасы',
                    'чехлы',
                    'аксессуары',
                    'смешанная',
                    'прогнозная',
                ];
                return str_replace($search, $replace, $attributes['elements_type']);
                // return str_replace($search, $replace, $value);

            }, //->shouldCache(), // ⬅️ Результат будет вычислен только один раз,
        );
    }



    // Relations: Связь с Клиентом
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Relations: Связь с Типом Заявки (оптовая, гар. ремонт и т.д.)
    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }


    // Relations: Связь с содержимым
    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    // public function delete(): void
    // {
    //     $this->lines()->delete();
    //     parent::delete();
    // }


}
