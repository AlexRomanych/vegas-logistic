<?php

namespace App\Models\Manufacture;

use App\Models\Abstract\Norm;
//use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CellNorm extends Norm
{


    public function __construct(int $normID = 0)
    {
        parent::__construct($normID);
    }


}
