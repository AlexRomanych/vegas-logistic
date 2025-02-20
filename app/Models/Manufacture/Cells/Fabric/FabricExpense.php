<?php

namespace App\Models\Manufacture\Cells\Fabric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricExpense extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    // Attract Связь со Заказом на ПС
    public function fabricOrder(): BelongsTo
    {
        return $this->belongsTo(FabricOrder::class);
    }
}
