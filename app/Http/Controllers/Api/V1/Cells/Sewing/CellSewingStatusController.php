<?php

namespace App\Http\Controllers\Api\V1\Cells\Sewing;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Sewing\Statuses\SewingTaskStatusResource;
use App\Models\Manufacture\Cells\Sewing\SewingTaskStatus;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

// use Illuminate\Http\Request;

class CellSewingStatusController extends Controller
{

    /**
     * ___ Возвращает список Статусов движения Заявки
     * @return AnonymousResourceCollection|string
     */
    public function getSewingTaskStatuses()
    {
        try {
            return SewingTaskStatusResource::collection(SewingTaskStatus::all());
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Обновляем цвет Типа Заявки
     * @param Request $request
     * @return string
     */
        public function patchSewingTaskStatusColor(Request $request)
    {
        try {
            $validated = $request->validate([
                'data'       => 'required|array',
                'data.color' => 'required|hex_color',
                'data.id'    => 'required|exists:sewing_task_statuses,id'
            ]);

            $validated = $validated['data'];

            SewingTaskStatus::query()->findOrFail($validated['id'])->update(['color' => $validated['color']]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }
}
