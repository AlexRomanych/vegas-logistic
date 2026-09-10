<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Assembly\Manage\AssemblyTaskLineSectorResource;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskLineSector;
use App\Services\Manufacture\AssemblyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class AssemblyTaskLineSectorController extends Controller
{


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---         Assembly Lines Sectors              !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    /**
     * ___ Устанавливаем статус Выполнено для Сущности Участка
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setAssemblyTaskLinesSectorDone(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'required|integer|exists:assembly_task_line_sectors,id',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = AssemblyTaskLineSector::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing Assembly Task Line Sector with id: ' . $id . '.');
                }

                $line->finished_at = now();
                $line->save();
            }

            // __ Устанавливаем статус самого СЗ
            AssemblyService::setAssemblyTaskStatus($validated['ids']);

            $sectors = AssemblyTaskLineSector::query()->whereIn('id', $validated['ids'])->get();
            return AssemblyTaskLineSectorResource::collection($sectors);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Устанавливаем статус Не выполнено для Сущности Участка
     * @param Request $request
     * @return AnonymousResourceCollection|string
     * @noinspection DuplicatedCode
     */
    public function setAssemblyTaskLinesSectorFalse(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'    => 'required|array',
                'ids.*'  => 'required|integer|exists:assembly_task_line_sectors,id',
                'reason' => 'required|string',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = AssemblyTaskLineSector::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing Assembly Task Line Sector with id: ' . $id . '.');
                }

                $line->false_at     = now();
                $line->false_reason = $validated['reason'];

                $history = $line->false_history;
                if (is_null($history)) {
                    $history = [];
                }

                $history[]           = [
                    'at'     => $line->false_at->format(RETURN_DATE_TIME_FORMAT),
                    'by'     => auth()->id(),
                    'reason' => $validated['reason'],
                ];
                $line->false_history = $history;
                $line->finished_at   = null;
                $line->save();
            }

            // __ Устанавливаем статус самого СЗ
            AssemblyService::setAssemblyTaskStatus($validated['ids']);

            $lines = AssemblyTaskLineSector::query()->whereIn('id', $validated['ids'])->get();
            return AssemblyTaskLineSectorResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Сбрасываем отметку Выполнено/Не выполнено для Сущности Участка
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setAssemblyTaskLinesSectorReset(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'required|integer|exists:assembly_task_line_sectors,id',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = AssemblyTaskLineSector::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing Assembly Task Line Sector with id: ' . $id . '.');
                }

                $line->finished_at  = null;
                $line->false_at     = null;
                $line->false_reason = null;
                $line->save();
            }

            // __ Устанавливаем статус самого СЗ
            AssemblyService::setAssemblyTaskStatus($validated['ids']);

            $lines = AssemblyTaskLineSector::query()->whereIn('id', $validated['ids'])->get();
            return AssemblyTaskLineSectorResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем Комментарий Сущности Участка
     * @param Request $request
     * @return string
     */
    public function setAssemblyTaskLineSectorDescription(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'          => 'required|integer|exists:assembly_task_line_sectors,id',
                'description' => 'nullable|string',
            ]);

            $description = $validated['description'] ?? null;
            AssemblyTaskLineSector::query()
                ->where('id', $validated['id'])
                ->update(['description' => $description]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
