<?php

namespace App\Models\Shared;


class Size
{
    public function __construct(
        public int $width  = 0,     // ширина
        public int $length = 0,     // длина
        public int $height = 0      // высота
    )
    {
    }
}
