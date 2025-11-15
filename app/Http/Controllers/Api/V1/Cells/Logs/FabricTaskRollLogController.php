<?php

namespace App\Http\Controllers\Api\V1\Cells\Logs;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Logs\FabricTaskRollLogResource;
use App\Models\Manufacture\Logs\FabricTaskRollLog;
use App\Services\DefaultsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FabricTaskRollLogController extends Controller
{
    /**
     * ___ Возвращает логи выполнения рулонов стежки за период
     * ___ Если период не указан, то возвращает логи за последний месяц
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getLogsFabricsExecuteRollsByPeriod(Request $request)
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
                $period = DefaultsService::getDefaultPeriodLogs();
                $start = Carbon::parse($period->getStart());
                $end = Carbon::parse($period->getEnd());
            }

            $logs = FabricTaskRollLog::query()
                ->whereBetween('created_at', [$start, $end])
                ->with(['fabricTaskRoll.fabric', 'user', 'responsible'])
                ->orderBy('log_at')
                ->get();

            return FabricTaskRollLogResource::collection($logs);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }
}
