<?php

namespace App\Models\Manufacture\Cells\Assembly;

use Illuminate\Database\Eloquent\Model;

class AssemblyTaskLineSector extends Model
{
    protected $guarded = false;

    protected $casts = [
        'expense'         => 'float',
        'rest'            => 'float',
        'total'           => 'float',
        'expense_per_pic' => 'float',
        'rest_per_pic'    => 'float',
        'total_per_pic'   => 'float',
    ];

    // --- -------------------------------
    // --- ---------- Scopes -------------
    // --- -------------------------------
    //public function scopeBySector($query, array|string|null $sectors = null)
    //{
    //    if (empty($sectors)) {
    //        return $query;
    //    }
    //
    //    if (is_string($sectors)) {
    //        $sectors = [$sectors];
    //    }
    //
    //
    //    return $query->whereIn('sector', $sectors);
    //        //$query->whereHas('modelType', function ($q) use ($sectors) {
    //        //    $q->whereIn('sector', $sectors);
    //        //});
    //}

}
