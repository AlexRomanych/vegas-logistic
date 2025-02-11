<?php

namespace App\Models\Abstract;


use App\Models\Manufacture\CellNorm;
use App\Models\Manufacture\CellsGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

abstract class Cell extends Model
{
    //  public int $no            номер ПЯ
    //  public string $name       название ПЯ
    //  public string $unit       единица измерения производительности ПЯ


//    public function __construct(
//        public int    $no = 0,
//        public string $name = '',
//        public string $unit = ''
//    )
//    {
//        parent::__construct();
//    }



    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    protected $with = ['cellNorm'];

    //attract: Привязываем к группе ПЯ
    public function cellsGroup(): BelongsTo
    {
        return $this->belongsTo(CellsGroup::class);
    }

    //attract: Привязываем ПЯ к норме
    public function cellNorm(): HasOne
    {
        return $this->hasOne(CellNorm::class);
    }




}
