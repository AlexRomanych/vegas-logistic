<?php

namespace App\Models\Models;

use App\Models\Materials\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelConstructItem extends Model
{
    protected $guarded = false;

    protected $casts = [
        'detail_height' => 'float',
        'amount'        => 'float',
        'position'      => 'integer',
    ];

    // Relations: Связь с Процедурой расчета
    public function procedure(): BelongsTo
    {
        return $this->belongsTo(ModelConstructProcedure::class, 'procedure_code_1c', CODE_1C);
    }

    // Relations: Связь с Материалом
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_code_1c', CODE_1C);
    }

}
