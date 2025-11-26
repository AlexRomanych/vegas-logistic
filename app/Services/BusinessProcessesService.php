<?php

namespace App\Services;


use App\Http\Resources\BusinessProcess\BusinessNodeForAdjacencyListResource;
use App\Models\BusinessProcesses\BusinessProcessNode;

class BusinessProcessesService
{

    /**
     * ___ Возвращает список смежности бизнес-процесса
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
            ->with(['previousNodes', 'nextNodes'])
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


}
