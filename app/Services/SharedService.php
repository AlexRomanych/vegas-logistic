<?php

namespace App\Services;


use App\Facades\Plan;
use App\Models\Shared\Period;
use Illuminate\Http\Request;

class SharedService
{

    /**
     * Проверяет на валидность поля выбора дат на страницах с выбором периода
     * @param Request $request
     * @return Period
     */
    public function validPlanDates(Request $request): Period
    {

        $result = $request->validate([
            'start-period' => ['nullable', 'before_or_equal:end-period'],
            'end-period' => ['nullable', 'after_or_equal:start-period'],
        ]);

        if (isset($result['start-period']) && isset($result['end-period'])) {
            return new Period($result['start-period'], $result['end-period']);
        } else {
            return Plan::getPeriod();
        }

    }
}
