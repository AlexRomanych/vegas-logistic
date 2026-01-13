<?php

namespace App\Http\Controllers\Api\V1\Cells\Sewing;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Sewing\SewingTaskManage\SewingTaskResource;
use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Services\DefaultsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class CellSewingController extends Controller
{

    // ___ Получаем СЗ на Пошив
    public function getSewingTasks(Request $request)
    {
        try {
            $validated = $request->validate([
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end   = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodPlanLoads();
                $start  = Carbon::parse($period->getStart());
                $end    = Carbon::parse($period->getEnd());
            }

            $sewingTasks = SewingTask::query()
                ->whereBetween('action_at', [
                    $start->startOfDay(),
                    $end->endOfDay()
                ])
                // ->whereDate('action_at', '>=', $start)     // Используем такую конструкцию, потому что
                // ->whereDate('action_at', '<=', $end)       // ->whereBetween() не включает периоды
                ->with([
                    'order.client', 'order.orderType',
                    'sewingLines.orderLine.model.cover',
                    'sewingLines.orderLine.model.base',
                    'statuses'
                ])
                // ->with(['sewingLines', 'sewingLines.orderLine','sewingLines.orderLine.model','sewingLines.orderLine.model.cover', 'statuses'])
                ->orderBy('action_at')
                ->get();


            return SewingTaskResource::collection($sewingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * Возвращает массив уникальных id заказов
     * @param  mixed  $data
     * @return array
     */
    private function getUniqueOrdersIds(mixed $data): array
    {
        $result = [];

        if (is_null($data) || is_bool($data)) {
            return $result;
        }

        foreach ($data as $item) {
            $result[] = $item['line']['order_id'];
        }


        return array_unique($result);
    }

}
