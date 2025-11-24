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
            $ALL_STATUSES_RANGE = '-100';

            $AVAILABLE_STATUSES_RANGE =
                $ALL_STATUSES_RANGE . ',' .       // -100 -
                FABRIC_ROLL_NULL_CODE . ',' .
                FABRIC_ROLL_CREATED_CODE . ',' .
                FABRIC_ROLL_RUNNING_CODE . ',' .
                FABRIC_ROLL_PAUSED_CODE . ',' .
                FABRIC_ROLL_DONE_CODE . ',' .
                FABRIC_ROLL_FALSE_CODE . ',' .
                FABRIC_ROLL_ROLLING_CODE . ',' .
                FABRIC_ROLL_MOVED_CODE . ',' .
                FABRIC_ROLL_CANCELLED_CODE . ',' .
                FABRIC_ROLL_CLOSED_CODE . ',';

            $validated = $request->validate([
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
                'status'       => 'nullable|numeric|in:' . $AVAILABLE_STATUSES_RANGE,
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodLogs();
                $start = Carbon::parse($period->getStart());
                $end = Carbon::parse($period->getEnd());
            }

            // $s_start = $start->format('Y-m-d H:i:s');
            // $s_end = $end->format('Y-m-d H:i:s');

            // __ Получаем статус из запроса. Если не передан, будет null.
            $status = $validated['status'] ?? null;

            $query = FabricTaskRollLog::query()
                ->whereDate('log_at', '>=', $start)     // Используем такую конструкцию, потому что
                ->whereDate('log_at', '<=', $end);      // ->whereBetween() не включает периоды

            // __ УСЛОВНАЯ ФИЛЬТРАЦИЯ С ПОМОЩЬЮ when()
            $logs = $query->when($request->has('status') && (int)$status != -100, function ($query) use ($status) {
                // Если условие (!is_null($status)) истинно, мы применяем фильтр.
                // Примечание: второй аргумент $statusValue в замыкании теперь не нужен,
                // так как мы используем $status напрямую через 'use'.
                $query->where('status_after', (int)$status);

            })
                ->with(['fabricTaskRoll.fabric', 'user', 'responsible'])
                ->orderBy('log_at', 'desc')
                ->get();

            // Метод when() выполнит замыкание, только если $status НЕ null, НЕ 0, НЕ false и т.д.
            // $logs = $query->when($status, function ($query, $statusValue) {
            //     // Эта часть кода выполняется, ТОЛЬКО если $status существует и не пуст.
            //     // $query - это текущий построитель запросов (FabricTaskRollLog::query())
            //     // $statusValue - это значение переменной $status
            //
            //     $query->where('status_after', $statusValue);
            //
            // })
            //     ->with(['fabricTaskRoll.fabric', 'user', 'responsible'])
            //     ->orderBy('log_at')
            //     ->get();


            // $logs = FabricTaskRollLog::query()
            //     ->whereDate('log_at', '>=', $start)     // Используем такую конструкцию, потому что
            //     ->whereDate('log_at', '<=', $end)       // ->whereBetween() не включает периоды
            //     // ->whereBetween('log_at', [$start, $end])
            //     ->with(['fabricTaskRoll.fabric', 'user', 'responsible'])
            //     ->orderBy('log_at')
            //     ->get();

            return FabricTaskRollLogResource::collection($logs);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Возвращает логи выполнения рулонов стежки по номеру рулона
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getLogsFabricsExecuteRollsByRollNumber(Request $request)
    {
        try {
            $validated = $request->validate([
                'roll' => 'required|numeric',
            ]);

            $rollNumber = $validated['roll'];

            $logs = FabricTaskRollLog::query()
                ->whereHas('fabricTaskRoll', function ($query) use ($rollNumber) {
                    $query->where('id', $rollNumber);
                })
                ->get();

            return FabricTaskRollLogResource::collection($logs);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }
}
