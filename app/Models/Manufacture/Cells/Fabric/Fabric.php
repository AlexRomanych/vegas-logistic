<?php

namespace App\Models\Manufacture\Cells\Fabric;

use App\Classes\FabricInstance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

//use Illuminate\Database\Eloquent\Relations\HasOne;

class Fabric extends Model
{
    private FabricInstance $fabricInstance;

    protected $guarded = [];

    protected $casts = [
        'average_roll_length' => 'float',
        'average_roll_length_hand' => 'float',
        'average_roll_length_statistic' => 'float',
    ];

    // Добавляем новые атрибуты
    protected $appends = [
        'pic',
        'textile',
        'fillersList',
        'fabricCorrect',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $with = [
        //        'fabricMachine'
        'fabricPicture'         // название как и в процедуре отношений
    ];

    //    public function __construct() {
    //        parent::__construct();
    //        $this->fabricInstance = new FabricInstance($this->getAttribute('name'));
    //
    //    }

    // h2 Определяем атрибуты
    public function getFabricInstanceAttribute(): FabricInstance
    {
        return new FabricInstance($this->name);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->fabric_instance->getFabricDisplayName();
    }

    public function getPicAttribute(): string
    {
        return $this->fabric_instance->getPicture();
    }

    public function getTextileAttribute(): string
    {
        return $this->fabric_instance->getTextileShortName();
    }

    public function getFillersListAttribute(): array
    {
        return $this->fabric_instance->getFillersList();
    }


    // attract: Проверяем наличие необходимых заполненных полей данных:
    // attract: 1. Средняя длина рулона
    // attract: 2. Производительность
    // attract: 3. Коэффициент перевода
    public function getFabricCorrectAttribute(): bool
    {

        $length = (float)$this->average_roll_length === 0.0;
        $translate = (float)$this->translate_rate === 0.0;
        $productivity = (float)$this->fabricPicture->productivity === 0.0 && (float)$this->productivity === 0.0;
        // $productivity = (float)$this->productivity === 0.0;

        return !($length || $productivity || $translate);

        //        return [
        //            'length' => (float)$this->average_roll_length === 0.0,
        //            'productivity' => (float)$this->productivity === 0.0,
        //            'translate' => (float)$this->translate_rate === 0.0,
        //            ];
        //        return (float)$this->average_roll_length !== 0.0 && (float)$this->productivity !== 0.0 && (float)$this->translate_rate !== 0.0;
    }


    // Relations: FabricPicture
    public function fabricPicture(): BelongsTo
    {
        return $this->belongsTo(FabricPicture::class);
    }


    // Relations: FabricOrder. Используем в Расходе ПС
    public function fabricOrder(): HasMany
    {
        return $this->hasMany(FabricOrder::class);
    }


    //    //h2 fabricMachine -----------------------------------------------------------------------------------------
    //    public function fabricMachine(): BelongsTo
    //    {
    //        return $this->belongsTo(FabricMachine::class);
    //    }

}
