<?php

namespace App\Models\Shared\Facades;

use App\Models\Shared\Period;
use Illuminate\Support\Carbon;

class PlanFacade
{

    /**
     * Возвращает период плана
     * Если разработка - возвращает из конфигурации
     * Если продакшн - считает по текущей дате
     * @return Period
     */
    public function getPeriod(): Period
    {
        if (env('APP_DEBUG')) {
            return new Period(
                config('vegas.general_plan_start'),
                config('vegas.general_plan_end'),
            );
        } else {
            $today = Carbon::today();

            return new Period(
                $today->firstOfMonth(),
                $today->lastOfMonth(),
            );
        }
    }

    /**
     * Возвращает период плана для рендеринга
     * Для отображения плана
     * @param string|Carbon|null $dateFrom
     * @param string|Carbon|null $dateTo
     * @return Period
     */
    public function getPeriodRender(string|Carbon $dateFrom = null, string|Carbon $dateTo = null): Period
    {
        $workPeriod = new Period($dateFrom, $dateTo);
        return new Period(
            $this->mondayBefore($workPeriod->getStart()),
            $this->sundayAfter($workPeriod->getEnd()),
        );
    }

    /**
     * Возвращает дату ближайшего предыдущего к дате понедельника
     * Если дата не указана, то это - начало плана
     * Для отображения плана
     * @param string|Carbon|null $inDate
     * @return Carbon
     */
    public function mondayBefore(string|Carbon $inDate = null): Carbon
    {
        if (! is_null($inDate)) {
            $workDate = $inDate instanceof Carbon ? $inDate : new Carbon($inDate);
        } else {
            $workDate = $this->getPeriod()->getStart();
        }

        while ($workDate->dayOfWeek !== 1) {
            $workDate = $workDate->subDay();
        }

        return $workDate;
    }

    /**
     * Возвращает дату ближайшего следующего к дате воскресенья
     * Если дата не указана, то это - окончание плана
     * Для отображения плана
     * @param string|Carbon|null $inDate
     * @return Carbon
     */
    public function sundayAfter(string|Carbon $inDate = null): Carbon
    {
        if (! is_null($inDate)) {
            $workDate = $inDate instanceof Carbon ? $inDate : new Carbon($inDate);
        } else {
            $workDate = $this->getPeriod()->getEnd();
        }

        while ($workDate->dayOfWeek !== 0) {
            $workDate = $workDate->addDay();
        }

        return $workDate;
    }

    /**
     * Возвращает целое количество недель между 2 датами
     * Если даты не заданы, берет данные плана
     * @param string|Carbon|Period|null $dateFrom
     * @param string|Carbon|null $dateTo
     * @return int
     */
    public function getWeeksAmount(string|Carbon|Period $dateFrom = null, string|Carbon $dateTo = null): int
    {

        if ($dateFrom instanceof Period) {
            $dateTo = $dateFrom->getEnd();
            $dateFrom = $dateFrom->getStart();
        }

        $period = (is_null($dateFrom) || is_null($dateTo)) ? $this->getPeriod() : new Period($dateFrom, $dateTo);

        return ceil(($period->getEnd()->diffInDays($period->getStart()))/7);
    }

    /**
     * Возвращает целое количество недель между 2 датами
     * Если даты не заданы, берет данные плана
     * @param string|Carbon|null $dateFrom
     * @param string|Carbon|null $dateTo
     * @return int
     */
    public function getLength(string|Carbon $dateFrom = null, string|Carbon $dateTo = null): int
    {
        $period = (is_null($dateFrom) || is_null($dateTo)) ? $this->getPeriod() : new Period($dateFrom, $dateTo);
        return $period->getEnd()->diffInDays($period->getStart());
    }
}
