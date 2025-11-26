<?php

namespace App\Models\BusinessProcesses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BusinessProcessNode extends Model
{
    protected $guarded = false;

    protected $hidden = ['created_at', 'updated_at'];

    // __ Первый узел для бизнес-процесса по id
    public function scopeFirstNode($query, $businessProcessId)
    {
        return $query
            ->whereHas('previousNodesConnections', function ($query) use ($businessProcessId) {
                return $query
                    ->where('business_process_id', $businessProcessId)
                    ->where('previous_node_id', null);
            });
    }

    // __ Последний узел для бизнес-процесса по id
    public function scopeLastNode($query, $businessProcessId)
    {
        return $query
            ->whereHas('nextNodesConnections', function ($query) use ($businessProcessId) {
                return $query
                    ->where('business_process_id', $businessProcessId)
                    ->where('next_node_id', null);
            });
    }

    // __ Узел для бизнес-процесса по id
    public function scopeInBusinessProcess(Builder $query, int $businessProcessId): Builder
    {
        // __ Создаем подзапрос для получения ID узлов, которые являются ИСТОЧНИКОМ
        $previousNodes = DB::table('business_process_nodes_connections')
            ->select('previous_node_id')
            ->where('business_process_id', $businessProcessId);

        // __ Создаем подзапрос для получения ID узлов, которые являются НАЗНАЧЕНИЕМ
        $nextNodes = DB::table('business_process_nodes_connections')
            ->select('next_node_id')
            ->where('business_process_id', $businessProcessId);

        // __ Объединяем результаты двух запросов
        $union = $previousNodes->union($nextNodes);

        // __ Применяем условие whereIn к основному запросу (модели Node)
        // __ используя все уникальные ID, найденные в связях графа.
        return $query->whereIn('id', $union);

        // __ То же самое, но без использования $nextNodes и $union
        // $nodeIdsInGraph = DB::table('business_process_nodes_connections')
        //     ->select('next_node_id')
        //     ->where('business_process_id', $businessProcessId)
        //     // Объединяем результаты двух запросов
        //     ->union($previousNodes);

        // return $query->whereIn('id', $nodeIdsInGraph);

    }


    // Relations: Связь с предыдущими узлом
    public function previousNodes(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                BusinessProcessNode::class,
                'business_process_nodes_connections',
                'next_node_id',
                'previous_node_id')
            ->using(BusinessProcessNodesConnection::class)
            ->withPivot(['order', 'active', 'status', 'business_process_id']);
    }

    // Relations: Связь с последующими узлом
    public function nextNodes(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                BusinessProcessNode::class,
                'business_process_nodes_connections',
                'previous_node_id',
                'next_node_id')
            ->using(BusinessProcessNodesConnection::class)
            ->withPivot(['order', 'active', 'status', 'business_process_id']);
    }


    // Relations: Связь с ребрами предыдущих узлов (incomingConnections - входящие ребра)
    public function previousNodesConnections(): HasMany
    {
        return $this->hasMany(BusinessProcessNodesConnection::class, 'next_node_id');
    }

    // Relations: Связь с ребрами последующих узлов (outgoingConnections - исходящие ребра)
    public function nextNodesConnections(): HasMany
    {
        return $this->hasMany(BusinessProcessNodesConnection::class, 'previous_node_id');
    }


}
