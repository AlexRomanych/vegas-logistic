<?php

namespace App\Http\Controllers\Api\V1\Cells\Sewing;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Sewing\Days\SewingDayResource;
use App\Models\Manufacture\Cells\Sewing\SewingDay;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CellSewingDayController extends Controller
{


    public function getSewingDayByDateAndChange(string $date, string $change)
    {
        try {
            $validated = Validator::make([
                'date'   => $date,
                'change' => $change,
            ], [
                'date'   => 'required|date_format:Y-m-d',
                'change' => 'required|in:1,2',
            ]);

            if ($validated->fails()) {
                throw new Exception($validated->errors()->first());
            }

            $day = SewingDay::query()
                ->with(['workers', 'responsible'])
                ->firstOrCreate([
                    'action_at' => Carbon::parse($date)->format(RETURN_DATE_TIME_FORMAT),
                    'action_at_str' => $date,
                    'change'        => $change,
                ]);


            return new SewingDayResource($day);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }
}
