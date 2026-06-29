<?php

namespace App\Http\Controllers\Api\V1\Processes;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessProcess\BusinessProcessForListResource;
use App\Models\BusinessProcesses\BusinessProcess;
use App\Models\BusinessProcesses\BusinessProcessNode;
use App\Models\BusinessProcesses\BusinessProcessNodesConnection;
use App\Models\BusinessProcesses\Defaults\ClientOrderMovingProcessDefault;
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
                0       => $result = BusinessProcess::query()->where('active', false)->with($with)->get(),
                1       => $result = BusinessProcess::query()->where('active', true)->with($with)->get(),
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
                ['id' => 'required|integer|exists:business_processes,id']
            );

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            return ['data' => BusinessProcessesService::getBusinessProcessAdjacencyList($id)];
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Установка значений по умолчанию
     * @return string
     */
    public function setBusinessProcessDefaults()
    {
        try {
            $data = [
                [
                    'id'                       => 1,
                    'client_id'                => 0,
                    'business_process_node_id' => 1,
                    'process_node_ref_id'      => 12,
                    'offset'                   => -5,
                    'day_offset_operation'     => false,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 2,
                    'client_id'                => 0,
                    'business_process_node_id' => 2,
                    'process_node_ref_id'      => 1,
                    'offset'                   => 0,
                    'day_offset_operation'     => null,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 3,
                    'client_id'                => 0,
                    'business_process_node_id' => 6,
                    'process_node_ref_id'      => 2,
                    'offset'                   => 1,
                    'day_offset_operation'     => true,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 4,
                    'client_id'                => 0,
                    'business_process_node_id' => 7,
                    'process_node_ref_id'      => 12,
                    'offset'                   => -3,
                    'day_offset_operation'     => false,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 5,
                    'client_id'                => 0,
                    'business_process_node_id' => 8,
                    'process_node_ref_id'      => 7,
                    'offset'                   => 1,
                    'day_offset_operation'     => true,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 6,
                    'client_id'                => 0,
                    'business_process_node_id' => 9,
                    'process_node_ref_id'      => 8,
                    'offset'                   => 1,
                    'day_offset_operation'     => true,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 7,
                    'client_id'                => 0,
                    'business_process_node_id' => 10,
                    'process_node_ref_id'      => 9,
                    'offset'                   => 0,
                    'day_offset_operation'     => null,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 8,
                    'client_id'                => 0,
                    'business_process_node_id' => 11,
                    'process_node_ref_id'      => 9,
                    'offset'                   => 0,
                    'day_offset_operation'     => null,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 9,
                    'client_id'                => 0,
                    'business_process_node_id' => 12,
                    'process_node_ref_id'      => 12,
                    'offset'                   => 0,
                    'day_offset_operation'     => null,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
                [
                    'id'                       => 10,
                    'client_id'                => 0,
                    'business_process_node_id' => 14,
                    'process_node_ref_id'      => 6,
                    'offset'                   => 1,
                    'day_offset_operation'     => true,
                    'is_interval'              => false,
                    'active'                   => true,
                    'created_at'               => '2026-03-25 18:15:06',
                    'updated_at'               => '2026-03-25 18:15:06',
                ],
            ];

            // Вставляем данные. Существующие строки (по индексу ID или уникальному ключу) будут проигнорированы.
            $insertedCount = ClientOrderMovingProcessDefault::query()->insertOrIgnore($data);


            $data          = [
                [
                    'id'                  => 11,
                    'business_process_id' => 1,
                    'previous_node_id'    => 6,
                    'next_node_id'        => 14,
                    'order'               => 5,
                    'active'              => true,

                ],
            ];
            $insertedCount = BusinessProcessNodesConnection::query()->insertOrIgnore($data);

            return EndPointStaticRequestAnswer::ok('Добавлено успешно');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
