<?php

namespace App\Models\Manufacture;

use App\Models\Abstract\Cell;
use App\Models\Norm\sewing\CellSewingAutoNorm;

//use Illuminate\Database\Eloquent\Model;

class CellItem extends Cell
{
    protected CellSewingAutoNorm $norm;

    public function __construct()
    {
        parent::__construct();
//        $this->norm = new CellSewingAutoNorm();
    }
}
