<?php

// ___ Производственная смена

namespace App\Classes;


use Carbon\Carbon;

class ManufactureDayAndChange
{

    private Carbon $manufactureDay;
    private int $change = 0;

    public function __construct(Carbon|string $manufactureDay, int $change)
    {
        $this->manufactureDay = normalizeToCarbon($manufactureDay);
        $this->change = $change;
    }



    public function getManufactureDay(): Carbon
    {
        return $this->manufactureDay;
    }


    public function getChange(): ?int
    {
        return $this->change !== 0 ? $this->change : null;
    }



}
