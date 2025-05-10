<?php

namespace App\Models\Manufacture\Cells\Fabric;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricOrder extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

//    protected $with = ['fabricsExpense'];


    // Relations: Связь со списком расходов
    public function fabricsExpense(): HasMany
    {
        return $this->hasMany(FabricExpense::class);
    }

    // Relations: Связь c клиентами
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
