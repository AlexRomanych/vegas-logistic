<?php

namespace App\Models\Abstract;

use App\Models\Manufacture\CellItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class Norm extends Model
{
    protected $table = 'cell_norms';
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    private int $normID;                        // ID нормы
    private null|CellItem $cellItem;            // Та ПЯ, к которой относится норма


    public function __construct($cellNormID = 0)
    {
        parent::__construct();
        $this->normID = $cellNormID;
        $this->cellItem = $this->getCellItem();
    }

    //attract: связываем с ПЯ
    public function cellItem(): BelongsTo
    {
        return $this->belongsTo(CellItem::class);
    }


    private function getCellItem(): null|CellItem
    {
        return CellItem::find($this->normID);
    }

}
