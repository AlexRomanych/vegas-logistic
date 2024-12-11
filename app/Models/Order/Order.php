<?php

namespace App\Models\Order;

use App\Models\Client;

use App\Models\Shared\Period;
use Illuminate\Database\Eloquent\Builder;
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


    //---------------------------------------------------------------------------------------------
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assemblyParts(): HasMany
    {
        return $this->hasMany(AssemblyPart::class);
    }

    public function sewingParts(): HasMany
    {
        return $this->hasMany(SewingPart::class);
    }

    public function cuttingParts(): HasMany
    {
        return $this->hasMany(CuttingPart::class);
    }

}
