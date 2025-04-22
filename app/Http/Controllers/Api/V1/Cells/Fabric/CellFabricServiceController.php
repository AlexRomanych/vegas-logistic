<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Services\Manufacture\FabricService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CellFabricServiceController extends Controller
{
    /**
     * descr: Endpoint: Возвращаем номер смены (бригады) на стежке по дате
     * @param Request $request
     * @return JsonResponse|string
     */
    public function getFabricTeamNumberByDate(Request $request)
    {
        try {

            // Если не приходит день, то отправляем текущий день
            $workDate = Carbon::now()->format('Y-m-d');

            if ($request->has('date')) {
                $validData = $request->validate([
                    'date' => 'date',
                ]);
                $workDate = $validData['date'];
            }

            $team = FabricService::getFabricTeamChangeNumberByDate($workDate);

            return response()->json([
                'data' => [
                    'date' => $workDate,
                    'team' => $team,
                ]
            ]);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

}
