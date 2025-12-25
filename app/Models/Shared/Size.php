<?php

namespace App\Models\Shared;


readonly class Size
{
    public function __construct(
        private int|null $width  = null,     // ширина
        private int|null $length = null,     // длина
        private int|null $height = null      // высота
    )
    {
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }
}
