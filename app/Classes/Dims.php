<?php

namespace App\Classes;

class Dims
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public float $width  = 0,     // ширина
        public float $length = 0,     // длина
        public float $height = 0      // высота
    )
    {
    }
}
