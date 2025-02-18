<?php

namespace App\Models\Manufacture\Cells\Fabric;

use App\Classes\FabricInstance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//use Illuminate\Database\Eloquent\Relations\HasOne;

class Fabric extends Model
{
    private FabricInstance $fabricInstance;

    protected $guarded = [];

    // Добавляем новые атрибуты
    protected $appends = [
        'pic',
        'textile',
        'fillersList'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $with = [
        'fabricMachine'
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

    //h2 fabricMachine -----------------------------------------------------------------------------------------
    public function fabricMachine(): BelongsTo
    {
        return $this->belongsTo(FabricMachine::class);
    }

}
