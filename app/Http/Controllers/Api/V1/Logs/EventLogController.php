<?php

namespace App\Http\Controllers\Api\V1\Logs;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Logs\EventLogResource;
use App\Models\Logs\EventLog;
use App\Services\DefaultsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventLogController extends Controller
{


    /**
     * ___ Возвращаем События приложения (Обновление справочников, расчета сырья и т.д.)
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getLogsAppEvents(Request $request)
    {
        try {
            $validated = $request->validate([
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodEventLogs();
                $start = Carbon::parse($period->getStart());
                $end = Carbon::parse($period->getEnd());
            }

            $eventLogs = EventLog::query()
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end)
                ->orderBy('created_at')
                ->get();

            return EventLogResource::collection($eventLogs);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::failResponse($e);
        }
    }
}
