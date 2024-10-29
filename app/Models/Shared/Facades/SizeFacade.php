<?php

namespace App\Models\Shared\Facades;


use App\Models\Shared\Size;
use Illuminate\Support\Str;

class SizeFacade
{

    /**
     * Проверяет, является ли модель форматкой
     * @param string|null $size
     * @return bool
     */
    public function isFormat(string|null $size = null): bool
    {
        if (is_null($size)) {
            return false;
        }
        $dims = $this->getDimensions($size);
        return ($dims->width <= FORMAT_LIMIT_WIDTH * 100 && $dims->length <= FORMAT_LIMIT_LENGTH * 100);
    }


    /**
     * Возвращаем корректную строку размера
     * @param string|null $size
     * @return string
     */
    public function correctSize(string|null $size = null): string
    {
        if (is_null($size)) {
            return '';
        }

        $size = trim(strtolower($size));

        while (Str::contains($size, ' ')) {
            $size = str_replace(' ', '', $size);
        }

        $size = str_replace(RUS_X, LAT_X, $size);
        $size = str_replace('-', LAT_X, $size);
        $size = str_replace('_', LAT_X, $size);
        $size = str_replace('.', LAT_X, $size);
        $size = str_replace(',', LAT_X, $size);
        $size = str_replace('/', LAT_X, $size);
        $size = str_replace('\\', LAT_X, $size);
        $size = str_replace('#', LAT_X, $size);

        $resStr = '';
        for ($i = 0; $i < mb_strlen($size); $i++) {
            $char = mb_substr($size, $i, 1);
            if (($char >= '0' && $char <='9') || $char === LAT_X) {
                $resStr = $resStr . $char;
            }
        }

        return $resStr;
    }


    /**
     * Разбивает строку размера на измерения
     * @param string|null $size
     * @return Size
     */
    public function getDimensions(string|null $size = null): Size
    {
        $size = $this->correctSize($size);
        $dims = explode(LAT_X, $size);
        return new Size(...$dims);
    }


}
