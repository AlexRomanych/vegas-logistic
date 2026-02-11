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

    /**
     *  __ Возвращаем периметр в сантиметрах
     * @return int
     */
    public function getPerimeter_SM(): int
    {
        return ($this->width + $this->length) * 2;
    }

    /**
     * __ Возвращаем периметр в метрах
     * @return float
     */
    public function getPerimeter(): float
    {
        return $this->getPerimeter_SM() / 100;
    }
}
