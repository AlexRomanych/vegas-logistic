<?php

namespace App\Models\Abstract;


use Illuminate\Database\Eloquent\Model;

abstract class Cell extends Model
{
    //  public int $no            номер ПЯ
    //  public string $name       название ПЯ
    //  public string $unit       единица измерения производительности ПЯ


    public function __construct(
        public int    $no = 0,
        public string $name = '',
        public string $unit = ''
    )
    {
        parent::__construct();
    }
}
