<?php

namespace App\Services;


use App\Models\Shared\Size;
use Illuminate\Support\Str;

class SizeService
{


    /**
     * ___ Возвращаем корректную строку размера
     * @param string|null $size
     * @return string
     */
    public static function correctSizeString(string|null $size = null): string
    {
        /** @noinspection DuplicatedCode */
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
    public static function getDimensions(string|null $size = null): Size
    {
        $size = self::correctSizeString($size);
        $dims = explode(LAT_X, $size);
        return new Size(...$dims);
    }
}
