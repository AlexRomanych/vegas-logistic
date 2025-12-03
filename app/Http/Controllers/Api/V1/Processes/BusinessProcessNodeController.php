<?php

namespace App\Http\Controllers\Api\V1\Processes;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessProcess\BusinessProcessNodeResource;
use App\Models\BusinessProcesses\BusinessProcessNode;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\BusinessProcess\BusinessProcessForListResource;
use App\Models\BusinessProcesses\BusinessProcess;

class BusinessProcessNodeController extends Controller
{
    /**
     * ___ Возвращает узел бизнес-процесса по id
     * @param string $id
     * @return BusinessProcessNodeResource|string
     */
    public function getBusinessProcessNodeById(string $id)
    {
        $a = 0;

        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|numeric|exists:business_process_nodes,id',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $businessProcessNode = BusinessProcessNode::query()->find($id);
            if (!$businessProcessNode) {
                throw new Exception('Business process Node with id ' . $id . ' not found');
            }

            return new BusinessProcessNodeResource($businessProcessNode);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }

    }
}
