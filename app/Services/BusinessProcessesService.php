<?php

namespace App\Services;


use App\Http\Resources\BusinessProcess\BusinessNodeForAdjacencyListResource;
use App\Models\BusinessProcesses\BusinessProcess;
use App\Models\BusinessProcesses\BusinessProcessNode;
use App\Models\BusinessProcesses\Defaults\ClientOrderMovingProcessDefault;
use Carbon\Carbon;

class BusinessProcessesService
{

    /**
     * ___ Возвращает список смежности для узлов бизнес-процесса
     * @param int $id id Бизнес-процесса
     * @return array
     */
    public static function getBusinessProcessAdjacencyList(int $id): array
    {
        // $startNode = BusinessProcessNode::query()->firstNode($id)->first();
        // $LastNode = BusinessProcessNode::query()->lastNode($id)->first();

        /** @noinspection PhpUndefinedMethodInspection */
        $allNodes = BusinessProcessNode::query()
            ->inBusinessProcess($id)
            ->with(['previousNodes', 'nextNodes', 'clientsDefaultSettings'])
            ->get();

        $adjList = [];

        foreach ($allNodes as $node) {
            $idx = $node->id;

            $adjList[$idx]['node'] = new BusinessNodeForAdjacencyListResource($node);

            $adjList[$idx]['in'] = [];
            $adjList[$idx]['out'] = [];

            foreach ($node->previousNodes as $inNode) {
                $adjList[$idx]['in'][] = !is_null($inNode) ? new BusinessNodeForAdjacencyListResource($inNode) : [];
            }

            foreach ($node->nextNodes as $outNode) {
                $adjList[$idx]['out'][] = !is_null($outNode) ? new BusinessNodeForAdjacencyListResource($outNode) : [];

            }
        }

        return $adjList;
    }


    /**
     * ___ Возвращает смещение для узла в бизнес-процессе Движение заявки в днях от конечного узла бизнес-процесса
     * @param int $nodeId
     * @param int $clientId
     * @return int
     */
    public static function getDateOffsetForOrderMovingProcessByNodeIdAndClientId(int $nodeId, int $clientId = 0): int
    {
        // $LastNode = BusinessProcessNode::query()->lastNode(ORDER_MOVING_BUSINESS_PROCESS_ID)->first();

        // __ Получаем id референсного узла
        $businessProcess = BusinessProcess::query()->find(ORDER_MOVING_BUSINESS_PROCESS_ID);
        if (!$businessProcess) {
            return 0;
        }
        $referenceNodeId =$businessProcess->reference_node_id;

        $currentDefault = ClientOrderMovingProcessDefault::query()
            ->where('client_id', $clientId)
            ->where('business_process_node_id', $nodeId)
            ->first();

        // __ Если нет настроек для клиента, то берем настройки по умолчанию
        if (!$currentDefault) {
            if ($clientId === 0) {
                return 0;
            }

            $clientId = 0;      // __ Переключаемся на настройки по умолчанию, явно задаем клиента по умолчанию
            $currentDefault = ClientOrderMovingProcessDefault::query()
                ->where('client_id', $clientId)
                ->where('business_process_node_id', $nodeId)
                ->first();

            if (!$currentDefault) { // __ Если что-то пошло не так, то возвращаем 0
                return 0;
            }
        }

        $offset = $currentDefault->offset;

        while ($currentDefault->process_node_ref_id !== $referenceNodeId) {
            $currentDefaultId = $currentDefault->process_node_ref_id;
            $currentDefault = ClientOrderMovingProcessDefault::query()
                ->where('client_id', $clientId)
                ->where('business_process_node_id', $currentDefaultId)
                ->first();

            if (!$currentDefault) {
                return 0;
            }

            $offset += $currentDefault->offset;
        }

        return $offset;
    }


}
