<?php

namespace App\Classes;


//use App\Dims;
use Illuminate\Support\Str;

class SizeStatic
{
    const FORMAT_LIMIT_WIDTH = 0.5;
    const FORMAT_LIMIT_LENGTH = 0.5;
    const RUS_X = 'х';
    const LAT_X = 'x';

    /**
     * Проверяет, является ли модель форматкой
     * @param string|null $size
     * @return bool
     */
    public static function isFormat(string|null $size = null): bool
    {
        if (is_null($size)) {
            return false;
        }
        $dims = self::getDimensionsInMeters($size);
        return ($dims->width <= self::FORMAT_LIMIT_WIDTH && $dims->length <= self::FORMAT_LIMIT_LENGTH);
    }


    /**
     * Возвращаем корректную строку размера
     * @param string|null $size
     * @return string
     */
    public static function correctSize(string|null $size = null): string
    {
        if (is_null($size)) {
            return '';
        }

        $size = trim(strtolower($size));

        while (Str::contains($size, ' ')) {
            $size = str_replace(' ', '', $size);
        }

        $size = str_replace(self::RUS_X, self::LAT_X, $size);
        $size = str_replace('-', self::LAT_X, $size);
        $size = str_replace('_', self::LAT_X, $size);
        $size = str_replace('.', self::LAT_X, $size);
        $size = str_replace(',', self::LAT_X, $size);
        $size = str_replace('/', self::LAT_X, $size);
        $size = str_replace('\\', self::LAT_X, $size);
        $size = str_replace('#', self::LAT_X, $size);

        $resStr = '';
        for ($i = 0; $i < mb_strlen($size); $i++) {
            $char = mb_substr($size, $i, 1);
            if (($char >= '0' && $char <= '9') || $char === self::LAT_X) {
                $resStr = $resStr . $char;
            }
        }

        return $resStr;
    }


    /**
     * Разбивает строку размера на измерения
     * @param string|null $size
     * @return Dims
     */
    public static function getDimensions(string|null $size = null): Dims
    {
        $size = self::correctSize($size);
        $dims = explode(self::LAT_X, $size);
        return new Dims(...$dims);
    }

    /**
     * Разбивает строку размера на измерения и возвращает в метрах
     * @param string|null $size
     * @return Dims
     */
    public static function getDimensionsInMeters(string|null $size = null): Dims
    {
        $size = self::correctSize($size);
        $dims = explode(self::LAT_X, $size);
        $dimsM = new Dims(...$dims);
        $dimsM->length = $dimsM->length / 100;
        $dimsM->width = $dimsM->width / 100;
        $dimsM->height = $dimsM->height / 100;

        return $dimsM;
    }

    /**
     * Возвращает периметр в м
     * @param string|Dims|null $size
     * @return float
     */
    public static function getPerimeter(string|Dims|null $size = null): float
    {
        if (is_null($size)) {
            return 0;
        }

        if ($size instanceof Dims) {
            return ($size->width + $size->length) * 2;
        }

        $dims = self::getDimensionsInMeters($size);
        return ($dims->width + $dims->length) * 2;
    }

    /**
     * Возвращает площадь в кв.м.
     * @param string|Dims|null $size
     * @return float
     */
    public static function getSquare(string|Dims|null $size = null): float
    {
        if (is_null($size)) {
            return 0;
        }

        if ($size instanceof Dims) {
            return ($size->width * $size->length);
        }

        $dims = self::getDimensionsInMeters($size);
        return ($dims->width * $dims->length);
    }
}
