<?php

namespace App\Models\Shared;




use Illuminate\Support\Carbon;

class Period
{
    private Carbon $start;
    private Carbon $end;


    public function __construct(string|Period|null $start = null, string|null $end = null)
    {
        if ($start instanceof Period) {
            $this->start = new Carbon($start->getStart());
            $this->end =  new Carbon($start->getEnd());

//            $this->start = $start->getStart() instanceof Carbon ? $start->getStart() : new Carbon($start->getStart());
//            $this->end = $start->getEnd() instanceof Carbon ? $start->getEnd() : new Carbon($start->getEnd());

        } else {
            $this->start = is_null($start) ? new Carbon() : new Carbon($start);
            $this->end = is_null($end) ? new Carbon() : new Carbon($end);
        }
    }

    public function getPeriodLength(): int
    {
        return isset($this->start) & isset($this->end) ? $this->end->diffInDays($this->start) + 1 : 0;
    }

    public function getStart(): mixed
    {
        return $this->start;
    }

    public function getEnd(): mixed
    {
        return $this->end;
    }

    public function setStart(mixed $start): void
    {
        $this->start = $start;
    }

    public function setEnd(mixed $end): void
    {
        $this->end = $end;
    }
}
