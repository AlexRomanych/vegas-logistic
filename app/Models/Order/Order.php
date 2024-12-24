<?php

namespace App\Models\Order;

use App\Models\Client;

//use App\Models\Shared\Period;
//use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'no_origin', 'no_num', 'status',
        'plan_period',
        'load_date', 'unload_date', 'unload_place',
        'description',
        'manager_start', 'manager_end',
        'design_start', 'design_end',
        'manager_load_date', 'manuf_date', 'responsible', 'comment',
        'service_json', 'meta',

        'client_id',
    ];

    protected $hidden = [
//        'id',
        'client_id',
        'created_at', 'updated_at',
    ];

    protected $with = [
        'client',
        'lines',
    ];

    //attract: Инфа в метадате
    //attract: дата старта ОДСП#дата конца ОДСП#дата пр-ва от ОДСП#дата загрузки от КС#ответственный от КС#комментарий#старт КС#конец КС
    //attract:
    //attract: meta[0] = дата старта ОПП                            start_date_opp
    //attract: meta[1] = дата конца ОПП                             end_date_opp
    //attract: meta[2] = дата производства от ОПП                   manufacture_date_opp
    //attract: meta[3] = дата загрузки на складе Вегаса от КС       load_date_ks
    //attract: meta[4] = ответственный от производства              responsible_1C
    //attract: meta[5] = комментарий к заявке                       comment_1C
    //attract: meta[6] = старт КС                                   start_date_ks
    //attract: meta[7] = конец КС                                   end_date_ks
    //attract:
    //attract: разделитель - '#'


    //attract: задаем вычисляемые свойства
    //массив для хранения распарсенных метаданных
    private array $metaData = [];

    protected $appends = [
        'start_date_opp', 'end_date_opp', 'manufacture_date_opp',
        'start_date_ks', 'end_date_ks', 'load_date_ks',
        'comment_1C', 'responsible_1C',
    ];


//    protected $appends = [
//        'assembly_parts'
//    ];


//    public function scopeAssembly(Builder $query, Period $period): void
//    {
//        $query->assemblyParts
//            ->where('manufacture_date', '>=', $period->getStart())
//            ->orWhere('manufacture_date', '<=', $period->getEnd());
//    }

    public function __construct()
    {
        parent::__construct();
        $this->getMetaData();
    }

    // Вычисляем метаданные
    protected function getMetaData(): void
    {

        if (!is_null($this->meta)) {
//            if (count($this->metaData) > 0) return;
            $parsedMeta = explode('#', $this->meta);
        } else {
            $parsedMeta = array_fill(0, 8, '');
        }

        $this->metaData = [
            'start_date_opp' => $parsedMeta[0],
            'end_date_opp' => $parsedMeta[1],
            'manufacture_date_opp' => $parsedMeta[2],
            'load_date_ks' => $parsedMeta[3],
            'responsible_1C' => $parsedMeta[4],
            'comment_1C' => $parsedMeta[5],
            'start_date_ks' => $parsedMeta[6],
            'end_date_ks' => $parsedMeta[7],
        ];
    }

    public function getStartDateOppAttribute(): string
    {
        $this->getMetaData();
        return $this->metaData['start_date_opp'];
    }

    public function getEndDateOppAttribute(): string
    {
        $this->getMetaData();
        return $this->metaData['end_date_opp'];
    }

    public function getManufactureDateOppAttribute(): string
    {
        $this->getMetaData();
        return $this->metaData['manufacture_date_opp'];
    }

    public function getLoadDateKsAttribute(): string
    {
        $this->getMetaData();
        return $this->metaData['load_date_ks'];
    }

    public function getResponsible1CAttribute(): string
    {
        $this->getMetaData();
        return $this->metaData['responsible_1C'];
    }

    public function getComment1CAttribute(): string
    {
        $this->getMetaData();
        return $this->metaData['comment_1C'];
    }

    public function getStartDateKsAttribute(): string
    {
        $this->getMetaData();
        return $this->metaData['start_date_ks'];
    }

    public function getEndDateKsAttribute(): string
    {
        $this->getMetaData();
        return $this->metaData['end_date_ks'];
    }

    //h2 Clients ---------------------------------------------------------------------------------------------
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }


    //h2 Lines ---------------------------------------------------------------------------------------------
    public function lines(): HasMany
    {
        return $this->hasMany(Line::class);
    }

    public function delete(): void
    {
        $this->lines()->delete();
        parent::delete();
    }

//
//    public function assemblyParts(): HasMany
//    {
//        return $this->hasMany(AssemblyPart::class);
//    }
//
//    public function sewingParts(): HasMany
//    {
//        return $this->hasMany(SewingPart::class);
//    }
//
//    public function cuttingParts(): HasMany
//    {
//        return $this->hasMany(CuttingPart::class);
//    }

}
