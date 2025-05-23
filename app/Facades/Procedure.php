<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
// * @method static Period getPeriod()
// * @method static Period getPeriodRender(string|Carbon $dateFrom = null, string|Carbon $dateTo = null)
// * @method static Period mondayBefore()
// * @method static Period sundayAfter()
// * @method static Period getWeeksAmount(string|Carbon|Period $dateFrom = null, string|Carbon $dateTo = null)
// * @method static Period getLength()
// *
// * @see
 */

class Procedure extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'procedure';
    }

}
