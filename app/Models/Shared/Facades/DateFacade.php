<?php

namespace App\Models\Shared\Facades;

use Illuminate\Support\Carbon;

class DateFacade
{

    /**
     * Возвращает русское название дня недели
     * @param Carbon|string $inDate     Входящая дата
     * @param bool $mode                Если true - возвращает сокращенные названия, в противном случае - полные
     * @return string
     */
    public function getRussianWeekDay(Carbon|string $inDate, bool $mode = true): string
    {
        if (! $inDate instanceof Carbon) {
            $inDate = new Carbon($inDate);
        }

        return match($inDate->dayOfWeek) {
            0 => $mode ? 'Вс' : 'Воскресенье',
            1 => $mode ? 'Пн' : 'Понедельник',
            2 => $mode ? 'Вт' : 'Вторник',
            3 => $mode ? 'Ср' : 'Среда',
            4 => $mode ? 'Чт' : 'Четверг',
            5 => $mode ? 'Пт' : 'Пятница',
            6 => $mode ? 'Сб' : 'Суббота',
        };

    }


    /**
     * Проверяет, является ли дата выходным днем или праздником
     * @param Carbon|string $inDate
     * @return bool
     */
    public function isHoliday(Carbon|string $inDate): bool
    {
        if (! $inDate instanceof Carbon) {
            $inDate = new Carbon($inDate);
        }

        $day = $inDate->format('d.m.Y');

        $blackDays = ['29.04.2023', '13.05.2023', '29.04.2023', '11.11.2023'];
        $redDays = ['02.01.2023', '08.03.2023', '24.04.2023', '25.04.2023', '01.05.2023', '08.05.2023', '09.05.2023', '03.07.2023', '06.11.2023', '07.11.2023', '25.12.2023'];

        if (in_array($day, $blackDays)) {
            return false;
        }

        if (in_array($day, $redDays)) {
            return true;
        }

        return $inDate->dayOfWeek === 6 || $inDate->dayOfWeek === 0;
    }

}
