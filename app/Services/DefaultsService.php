<?php

namespace App\Services;


use App\Classes\Period;
use Carbon\Carbon;

final class DefaultsService
{

    /**
     * ___ Возвращает период логов по умолчанию
     * @return Period
     */
    public static function getDefaultPeriodLogs(): Period
    {
        // __ - 1 месяц назад - сегодня
        return new Period(Carbon::now()->subMonth(), Carbon::now());

        // __ - 2 месяца назад - сегодня
        // return new Period(Carbon::now()->subMonths(2), Carbon::now());

        // __ Начало месяца - сегодня
        // return new Period(Carbon::now()->startOfMonth(), Carbon::now());
    }


    /**
     * ___ Возвращает период планов по умолчанию
     * @return Period
     */
    public static function getDefaultPeriodPlan(): Period
    {
        return self::getDefaultPeriodPlanLoads();
    }

    /**
     * ___ Возвращает период логов по умолчанию
     * @return Period
     */
    public static function getDefaultPeriodPlanLoads(): Period
    {
        // __ Начало текущего месяца + 2 месяца вперед
        return new Period(Carbon::now()->startOfMonth(), Carbon::now()->addMonths(2)->endOfMonth());
    }



}
