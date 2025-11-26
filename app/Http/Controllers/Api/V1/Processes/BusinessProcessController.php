<?php

namespace App\Http\Controllers\Api\V1\Processes;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessProcess\BusinessProcessForListResource;
use App\Models\BusinessProcesses\BusinessProcess;
use App\Services\BusinessProcessesService;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BusinessProcessController extends Controller
{

    /**
     * ___ Отдаем список Бизнес процессов
     * @param $status : нет (null) - все, 0 (false) - неактивные, 1 (true) - активные
     * @return AnonymousResourceCollection|string
     */
    public function getBusinessProcesses($status = null)
    {
        try {
            $validator = validator([
                'status' => (int)$status,
            ], [
                'status' => 'nullable|in:0,1',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $status = is_null($status) ? -1 : (int)$status;

            $with = ['referenceNode', 'startNode', 'finishNode'];

            match ($status) {
                0 => $result = BusinessProcess::query()->where('active', false)->with($with)->get(),
                1 => $result = BusinessProcess::query()->where('active', true)->with($with)->get(),
                default => $result = BusinessProcess::query()->with($with)->get(),
            };

            return BusinessProcessForListResource::collection($result);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Возвращает бизнес процесс по id
     * @param string $id
     * @return BusinessProcessForListResource|string
     */
    public function getBusinessProcessById(string $id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|numeric|exists:business_processes,id',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $businessProcess = BusinessProcess::query()->find($id);
            if (!$businessProcess) {
                throw new Exception('Business process with id ' . $id . ' not found');
            }

            return new BusinessProcessForListResource($businessProcess);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }

    }


    /**
     * ___ Получаем список Список Смежности бизнес-процесса
     * @param string $id id бизнес процесса
     * @return array|string
     */
    public function getBusinessProcessAdjacencyList(string $id)
    {
        try {

            $validator = Validator::make(
                ['id' => $id],
                ['id' => 'required|numeric|exists:business_processes,id']
            );

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            return ['data' => BusinessProcessesService::getBusinessProcessAdjacencyList($id)];
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }

    }


}
