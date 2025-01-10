<?php

namespace App\Models\Norm\sewing;

use App\Classes\SizeStatic;
use App\Models\Manufacture\CellNorm;

class CellSewingAutoNorm extends CellNorm
{
    const CELL_SEWING_AUTO_NORM_ID = 9;                 // id ПЯ в базе

    const CELL_SEWING_AUTO_NORM_CONST = 48.6;           // Константа для расчета нормы - производительность одной швейной машины АШМ
    const MACHINES_COUNT = 3;                           // Количество машин

    public function __construct()
    {
        parent::__construct(self::CELL_SEWING_AUTO_NORM_ID);
    }

    /**
     * Выдаем сам расчет нормы
     * Если количество не равно нулю, то возвращаем норму за количество,
     * если равно 0 нулю, то возвращаем норму за единицу
     *
     * @param string $size
     * @param string $modelName
     * @param int $amount
     * @return float
     */

    //
    public function calculateNorm(string $size = '', string $modelName = '', int $amount = 0): float
    {
        if ($size === '' && $modelName === '') {
            return 0;
        }

        $perimeter = SizeStatic::getPerimeter($size);

        $normPerPic = $perimeter * 2 / (self::MACHINES_COUNT * self::CELL_SEWING_AUTO_NORM_CONST);
        return $amount === 0 ? $normPerPic : $amount * $normPerPic;
    }



}
