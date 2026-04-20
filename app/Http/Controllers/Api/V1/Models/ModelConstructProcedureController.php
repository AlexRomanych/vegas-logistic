<?php

namespace App\Http\Controllers\Api\V1\Models;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Model\ModelConstructProcedureResource;
use App\Models\Models\ModelConstructProcedure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class ModelConstructProcedureController extends Controller
{

    /**
     * ___ Загрузка / обновление процедур расчета
     * @param Request $request
     * @return string
     */
    public function modelConstructProceduresUpload(Request $request)
    {

        try {
            $data = $request->validate(['data' => 'required|json']);

            $data = json_decode($data['data'], true);

            foreach ($data as $item) {

                $procedure = ModelConstructProcedure::query()->updateOrCreate(
                    [
                        CODE_1C => $item[CODE_1C],
                    ],
                    [
                        'name'           => $item['name'],
                        'text'           => $item['text'],
                        'object_code_1c' => $item['object_code_1c'],
                        'object_name'    => $item['object_name'],
                    ],
                );
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }

    }


    /**
     * ___ Получение процедур расчета
     * @return AnonymousResourceCollection|string
     */
    public function getModelProcedures()
    {
        try {
            $procedures = ModelConstructProcedure::query()->get();
            return ModelConstructProcedureResource::collection($procedures);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получение процедуры расчета
     * @param string $code1c
     * @return ModelConstructProcedureResource|false|string
     */
    public function getModelProcedure(string $code1c)
    {
        try {
            $validated = Validator::make([
                'code1c' => $code1c
            ], [
                'code1c' => 'required|string'
            ])->validated();

            $procedure = ModelConstructProcedure::query()
                ->find($validated['code1c']);

            // __ Отдаем отдельный ресурс, не тот, который для рендера
            return $procedure ? new ModelConstructProcedureResource($procedure) : json_encode(['data' => null]);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
