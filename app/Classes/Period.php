<?php

namespace App\Classes;


use Carbon\Carbon;

class Period
{
    private Carbon $start, $end;

    public function __construct($start = null, $end = null)
    {
        if (is_null($start)) {
            $this->start = Carbon::now();
        } else {
            $this->start = $start instanceof Carbon ? $start : Carbon::parse($start);
        }

        if (is_null($end)) {
            $this->end = Carbon::now();
        } else {
            $this->end = $end instanceof Carbon ? $end : Carbon::parse($end);
        }
    }

    /**
     * ___ Возвращаем период в виде массива
     * @return array[]
     */
    public function getPeriod(): array
    {
        return
            [
                'period' =>
                    [
                        'start' => $this->start,
                        'end'   => $this->end
                    ]
            ];
    }


    /**
     * ___ Возвращаем начало периода
     * @return Carbon
     */
    public function getStart(): Carbon
    {
        return $this->start;
    }


    /**
     * ___ Возвращаем конец периода
     * @return Carbon
     */
    public function getEnd(): Carbon
    {
        return $this->end;
    }

}
