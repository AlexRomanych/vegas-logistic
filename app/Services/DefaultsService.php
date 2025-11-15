<?php

namespace App\Services;


use App\Classes\Period;
use Carbon\Carbon;

final class DefaultsService
{
    public static function getDefaultPeriodLogs(): Period
    {
        // __ - 1 месяц назад - сегодня
        return new Period(Carbon::now()->subMonth(), Carbon::now());

        // __ - 2 месяца назад - сегодня
        // return new Period(Carbon::now()->subMonths(2), Carbon::now());

        // __ Начало месяца - сегодня
        // return new Period(Carbon::now()->startOfMonth(), Carbon::now());
    }



}
