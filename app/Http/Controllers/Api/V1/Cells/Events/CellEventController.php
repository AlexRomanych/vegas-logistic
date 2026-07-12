<?php

namespace App\Http\Controllers\Api\V1\Cells\Events;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Events\CellEventResource;
use App\Models\Manufacture\Cells\Block\BlockDay;
use App\Models\Manufacture\Cells\Cutting\CuttingDay;
use App\Models\Manufacture\Cells\Sewing\SewingDay;
use App\Models\Manufacture\Events\CellEvent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CellEventController extends Controller
{

    /**
     * ___ Получаем Журнал Событий
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getEvents(Request $request)
    {
        try {
            $data = $request->validate([
                'day'  => 'required|integer|min:1',
                'cell' => 'required|string|in:' .
                    CellEvent::CELL_BLOCKS . ',' .
                    CellEvent::CELL_SEWING . ',' .
                    CellEvent::CELL_CUTTING . ',' .
                    CellEvent::CELL_FABRIC,
            ]);

            $cellEvents = CellEvent::query()
                ->dayEvents($data['day'], $data['cell'])
                ->get();


            return CellEventResource::collection($cellEvents);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем Запись в Журнале Событий
     * @param Request $request
     * @return CellEventResource|string
     */
    public function updateEvent(Request $request)
    {
        try {
            //$all  = $request->all();

            $validated = $request->validate([
                'data.id'        => 'required|integer|exists:cell_events,id',
                'data.start_at'  => 'required|string|date_format:Y-m-d H:i:s',
                'data.finish_at' => 'required|string|date_format:Y-m-d H:i:s|after:data.start_at',
                'data.event'     => 'required|string|min:1',
                'data.cell'      => 'required|string|in:' .
                    CellEvent::CELL_BLOCKS . ',' .
                    CellEvent::CELL_SEWING . ',' .
                    CellEvent::CELL_CUTTING . ',' .
                    CellEvent::CELL_FABRIC,
            ]);

            $cellEvent = CellEvent::query()->findOrFail($validated['data']['id']);

            $updateData = [
                'start_at'  => $validated['data']['start_at'],
                'finish_at' => $validated['data']['finish_at'],
                'cell'      => $validated['data']['cell'],
                'event'     => $validated['data']['event'],
            ];

            $cellEvent->update($updateData);

            return new CellEventResource($cellEvent);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создаем Запись в Журнале Событий
     * @param Request $request
     * @return CellEventResource|string
     */
    public function createEvent(Request $request)
    {
        try {
            //$all  = $request->all();

            $validated = $request->validate([
                'data.day'       => 'required|integer|min:1',
                'data.start_at'  => 'required|string|date_format:Y-m-d H:i:s',
                'data.finish_at' => 'required|string|date_format:Y-m-d H:i:s|after:data.start_at',
                'data.event'     => 'required|string|min:1',
                'data.cell'      => 'required|string|in:' .
                    CellEvent::CELL_BLOCKS . ',' .
                    CellEvent::CELL_SEWING . ',' .
                    CellEvent::CELL_CUTTING . ',' .
                    CellEvent::CELL_FABRIC,
            ]);

            // __ Проверяем га день
            $day = match ($validated['data']['cell']) {
                CellEvent::CELL_BLOCKS  => BlockDay::query()->findOrFail($validated['data']['day']),
                CellEvent::CELL_SEWING  => SewingDay::query()->findOrFail($validated['data']['day']),
                CellEvent::CELL_CUTTING => CuttingDay::query()->findOrFail($validated['data']['day']),
                //CellEvent::CELL_FABRIC  => FabricDay::query()->findOrFail($validated['data']['day']),
                default                 => null
            };

            if (!$day) {
                throw new Exception('Day not found');
            }

            $createData = [
                'day_id'    => $day->id,
                'start_at'  => $validated['data']['start_at'],
                'finish_at' => $validated['data']['finish_at'],
                'cell'      => $validated['data']['cell'],
                'event'     => $validated['data']['event'],
            ];

            $cellEvent = CellEvent::query()->create($createData);

            return new CellEventResource($cellEvent);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем Запись в Журнале Событий
     * @param Request $request
     * @return string
     */
    public function deleteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'data.id'        => 'required|integer|exists:cell_events,id',
                'data.start_at'  => 'required|string|date_format:Y-m-d H:i:s',
                'data.finish_at' => 'required|string|date_format:Y-m-d H:i:s|after:data.start_at',
                'data.event'     => 'required|string|min:1',
                'data.cell'      => 'required|string|in:' .
                    CellEvent::CELL_BLOCKS . ',' .
                    CellEvent::CELL_SEWING . ',' .
                    CellEvent::CELL_CUTTING . ',' .
                    CellEvent::CELL_FABRIC,
            ]);

            $cellEvent = CellEvent::query()->findOrFail($validated['data']['id']);
            $cellEvent->delete();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
