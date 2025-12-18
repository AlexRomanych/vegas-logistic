<?php

namespace App\Http\Controllers\Api\V1\Models;

use App\Classes\EndPointStaticRequestAnswer;
use App\Enums\ConstructTypes;
use App\Http\Controllers\Controller;
use App\Models\Models\ModelConstruct;
use App\Models\Models\ModelConstructItem;
use App\Services\MaterialsService;
use App\Services\ModelsService;
use App\Services\ProceduresService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ModelConstructController extends Controller
{

    public function getModelConstructs()
    {

    }


    /**
     * ___ Обновляем спецификации моделей
     * @param Request $request
     * @return string
     * @throws Throwable
     */
    public function modelConstructsUpload(Request $request)
    {
        try {
            $data = $request->validate(['data' => 'required|json']);

            $data = json_decode($data['data'], true);

            // TODO: Очищаем таблицу. Продумать тут целесообразность ее удаления
            DB::table('model_construct_items')->truncate();

            $missingModels = [];
            $missingMaterials = [];
            $missingProcedures = [];

            foreach ($data as $modelConstruct) {

                // TODO: Обновляем чехол по сути по информации о чехле, которая есть в таблице моделей
                // TODO: Проверить на достаточность, если нет, то можно попробовать по имени поискать чехол
                // __ Получаем модель или чехол
                $model = ModelsService::getModelByCode1C($modelConstruct['mc']);                // mc - model construct
                $modelCover = ModelsService::getModelCoverByCode1C($modelConstruct['mc']);      // mc - model construct

                $constructType = null;

                if ($model) {      // Заполняем спецификации только для существующих моделей
                    $constructType = ConstructTypes::BASE->value;
                } else if ($modelCover) {
                    $constructType = ConstructTypes::COVER->value;
                    $model = $modelCover;  // Переопределяем модель для заполнения спецификаций для удобства
                } else {
                    $missingModels[] = [
                        'code_1c' => $modelConstruct['mc'],
                        'name'    => $modelConstruct['mn'],
                    ];
                    continue;
                }

                // TODO: Добавить проверку на совпадение спецификации со входящими данными для уменьшения количества запросов в БД

                /** @noinspection PhpUndefinedFieldInspection */
                /*$createdModelConstruct = */ModelConstruct::query()->updateOrCreate(
                    [
                        CODE_1C => $modelConstruct['cc'],                           // cc - construct code 1c
                    ],
                    [
                        'name'          => $modelConstruct['cn'],                            // cn - construct name
                        'model_code_1c' => $model->code_1c, // тут принадлежность к модели и для чехла, благодаря подмене
                        // 'model_code_1c' => $modelConstruct['mc'],                // mc - model code 1c
                        'model_name'    => $modelConstruct['mn'],                   // mn - model name
                        'type'          => $constructType,
                    ],
                );

                DB::transaction(function () use ($modelConstruct, &$missingMaterials, &$missingProcedures) {
                    // __ Удаляем старые записи по составу спецификации
                    ModelConstructItem::query()
                        ->where('construct_code_1c', $modelConstruct['cc'])
                        ->delete();

                    foreach ($modelConstruct['items'] as $modelConstructItem) {

                        $modelConstructItem['c'] = mb_substr($modelConstructItem['c'], -9); // Обрезаем код 1с до 9 символов, потому что есть косяки

                        $material = MaterialsService::getMaterialByCode1C($modelConstructItem['c']);    // c - material code 1c
                        $procedure = ProceduresService::getProcedureByCode1C($modelConstructItem['k']); // k - procedure code 1c

                        // __ Собираем список отсутствующих материалов
                        if (!$material) {
                            $missingMaterials[$modelConstructItem['c']] = $modelConstructItem['n'];
                        }

                        // __ Собираем список отсутствующих процедур
                        if (!$procedure) {
                            $missingProcedures[$modelConstructItem['k']] = $modelConstructItem['p'];
                        }

                        // __ Создаем новый состав спецификации
                        // TODO: Удалить старые записи по составу спецификации (нужна проверка и доработка)
                        // TODO: Или удалять все записи сразу в таблице
                        // ModelConstructItem::query()->where('construct_code_1c', $modelConstruct['cc'])->delete();

                        /** @noinspection PhpUndefinedFieldInspection */
                        /*$createdModelConstructItem =*/
                        ModelConstructItem::query()->create(
                            [
                                'construct_code_1c'      => $modelConstruct['cc'],       // cc - construct code 1c
                                'material_code_1c'       => $material?->code_1c,
                                'material_code_1c_copy'  => $modelConstructItem['c'],    // c - material code 1c
                                'material_name'          => $modelConstructItem['n'],    // n - material name
                                'material_unit'          => $modelConstructItem['u'],    // u - material unit
                                'detail'                 => $modelConstructItem['d'] !== '' ? $modelConstructItem['d'] : null,    // d - detail
                                'detail_height'          => $modelConstructItem['h'],    // h - detail height
                                'procedure_code_1c'      => $procedure?->code_1c,
                                'procedure_code_1c_copy' => $modelConstructItem['k'] !== '' ? $modelConstructItem['k'] : null,
                                'procedure_name'         => $modelConstructItem['p'] !== '' ? $modelConstructItem['p'] : null,    // p - procedure name
                                'amount'                 => $modelConstructItem['a'],    // a - amount
                                'position'               => $modelConstructItem['o'],    // o - order
                            ],
                        );
                    }

                }, 2);  // 2 попытки

            }

            // TODO: Сделать обработку пропущенных материалов
            // TODO: (например занести их в таблицу материалов в категорию ручной обработки)
            $a = $missingMaterials;

            // TODO: Сделать обработку пропущенных процедур
            $b = $missingProcedures;

            // TODO: Сделать обработку пропущенных моделей
            // TODO: например, добавлять их в таблицу моделей, например чехлы
            $c = $missingModels;

            // return json_encode($missingModels);
            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
