<?php

namespace App\Facades;


use App\Models\Shared\Period;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Period getPeriod()
 * @method static Period getPeriodRender(string|Carbon $dateFrom = null, string|Carbon $dateTo = null)
 * @method static Period mondayBefore()
 * @method static Period sundayAfter()
 * @method static int getWeeksAmount(string|Carbon|Period $dateFrom = null, string|Carbon $dateTo = null)
 * @method static Period getLength()
 *
 * @see
 */

class Plan extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'plan';
    }

}
