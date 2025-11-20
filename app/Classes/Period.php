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

        // __ Проверяем, что начало периода меньше конца и меняем местами по необходимости
        if ($this->start->greaterThan($this->end)) {
            $temp = $this->start;
            $this->start = $this->end;
            $this->end = $temp;
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
     * ___ Возвращаем период в виде JSON
     * @return string
     */
    public function toJson(): string
    {
        return json_encode(
            [
                'period' =>
                    [
                        'start' => $this->start->format('Y-m-d H:i:s'),
                        'end'   => $this->end->format('Y-m-d H:i:s'),
                    ]
            ]
        );
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
