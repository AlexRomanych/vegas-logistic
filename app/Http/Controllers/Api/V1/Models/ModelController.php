<?php

namespace App\Http\Controllers\Api\V1\Models;

use App\Classes\EndPointStaticRequestAnswer;
use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\Controller;
use App\Http\Resources\Model\ModelResource;
use App\Models\Models\Model;
use App\Models\Models\ModelCollection;
use App\Models\Models\ModelManufactureStatus;
use App\Models\Models\ModelManufactureType;
use App\Models\Models\ModelType;
use App\Services\CollectionsService;
use App\Services\ModelsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Throwable;

class ModelController extends Controller
{


    /**
     * ___ Обновление моделей
     * @throws Throwable
     */
    public function modelsUpload(Request $request)
    {

        try {
            $data = $request->validate(['data' => 'required|json']);

            $data = json_decode($data['data'], true);

            // __ Сбрасываем все active статусы моделей
            ModelsService::setActiveToFalseForModelsEntities();

            foreach ($data as $item) {

                // __ Вставляем новую коллекцию, если она не существует
                $modelsCollection = ModelsService::createOrUpdateModelEntity(
                    ModelCollection::class,
                    [
                        'model_collection_code_1c' => $item['model_collection_code_1c'],
                        'model_collection_name'    => $item['model_collection_name'],
                    ]
                );

                if (!$modelsCollection) {
                    throw new Exception(
                        'Fail to insert model collection: '
                        . $item['model_collection_code_1c']
                        . ' => '
                        . $item['model_collection_name']);
                }

                // __ Вставляем новый тип модели, если он не существует, иначе обновляем
                $modelType = ModelsService::createOrUpdateModelEntity(
                    ModelType::class,
                    [
                        'model_type_code_1c' => $item['model_type_code_1c'],
                        'model_type_name'    => $item['model_type_name'],
                    ]
                );

                if (!$modelType) {
                    throw new Exception(
                        'Fail to insert model type: '
                        . $item['model_type_code_1c']
                        . ' => '
                        . $item['model_type_name']);
                }

                // __ Вставляем новый Тип производства, если он не существует, иначе обновляем
                $modelManufactureType = ModelsService::createOrUpdateModelEntity(
                    ModelManufactureType::class,
                    [
                        'model_manufacture_type_code_1c' => $item['model_manufacture_type_code_1c'],
                        'model_manufacture_type_name'    => $item['model_manufacture_type_name'],
                    ]
                );

                if (!$modelManufactureType) {
                    throw new Exception(
                        'Fail to insert model manufacture type: '
                        . $item['model_manufacture_type_code_1c']
                        . ' => ' . $item['model_manufacture_type_name']);
                }

                // __ Вставляем новый Статус производства, если он не существует, иначе обновляем
                $modelManufactureStatus = ModelsService::createOrUpdateModelEntity(
                    ModelManufactureStatus::class,
                    [
                        'model_manufacture_status_id'   => $item['model_manufacture_status_id'],
                        'model_manufacture_status_name' => $item['model_manufacture_status_name'],
                    ]
                );

                if (!$modelManufactureStatus) {
                    throw new Exception(
                        'Fail to insert model manufacture status: '
                        . $item['model_manufacture_status_id']
                        . ' => '
                        . $item['model_manufacture_status_name']);
                }

                // __ Проверяем есть ли Группа сортировки производства в базе
                $modelManufactureGroup = ModelsService::getModelManufactureGroupById($item['model_manufacture_group_id']);

                if (!$modelManufactureGroup) {
                    throw new Exception(
                        'Fail to find model manufacture group: '
                        . $item['model_manufacture_group_id']
                    // . ' =>'
                    // . $item['model_manufacture_type_name']
                    );
                }


                // $modelsCollection = ModelsService::getModelsCollectionByCode1C($item['model_collection_code_1c']);
                // if (!$modelsCollection || $modelsCollection->name !== $item['model_collection_name']) {
                //     $insertedModelsCollection = ModelCollection::query()->updateOrCreate(
                //         [
                //             CODE_1C => $item['model_collection_code_1c'],
                //         ],
                //         [
                //             'name' => $item['model_collection_name'],
                //         ]
                //     );
                //
                //     if (!$insertedModelsCollection) {
                //         throw new Exception(
                //             'Fail to insert model collection: ' .
                //             $item['model_collection_id'] . ' =>' .
                //             $item['model_collection_name']);
                //     }
                // }

                $model = Model::query()->updateOrCreate(
                    [
                        CODE_1C => $item[CODE_1C],
                    ],
                    [
                        'model_manufacture_status_id' => $item['model_manufacture_status_id'],
                        // 'model_manufacture_status_name' => $item['model_status_name'],

                        'model_collection_code_1c' => $item['model_collection_code_1c'],
                        // 'model_collection_name'       => $item['model_collection_name'],

                        'model_type_code_1c' => $item['model_type_code_1c'],
                        // 'model_type_name'             => $item['model_type_name'],

                        'serial'              => $item['serial'] === '' ? null : $item['serial'],
                        'name'                => $item['name'],
                        'name_short'          => $item['name_short'] === '' ? null : $item['name_short'],
                        'name_common'         => $item['name_common'] === '' ? null : $item['name_common'],
                        'name_report'         => $item['name_report'] === '' ? null : $item['name_report'],
                        'cover_code_1c'       => $item['cover_code_1c'] === '' ? null : $item['cover_code_1c'],
                        'cover_name_1c'       => $item['cover_name_1c'] === '' ? null : $item['cover_name_1c'],
                        'base_height'         => $item['base_height'],
                        'cover_height'        => $item['cover_height'],
                        'textile'             => $item['textile'] === '' ? null : $item['textile'],
                        'textile_composition' => $item['textile_composition'] === '' ? null : $item['textile_composition'],
                        'cover_type'          => $item['cover_type'] === '' ? null : $item['cover_type'],
                        'zipper'              => $item['zipper'] === '' ? null : $item['zipper'],
                        'spacer'              => $item['spacer'] === '' ? null : $item['spacer'],
                        'stitch_pattern'      => $item['stitch_pattern'] === '' ? null : $item['stitch_pattern'],
                        'pack_type'           => $item['pack_type'] === '' ? null : $item['pack_type'],
                        'base_composition'    => $item['base_composition'] === '' ? null : $item['base_composition'],
                        'side_foam'           => $item['side_foam'] === '' ? null : $item['side_foam'],
                        'base_block'          => $item['base_block'] === '' ? null : $item['base_block'],
                        'load'                => $item['load'] === 0 ? null : $item['load'],
                        'guarantee'           => $item['guarantee'] === 0 ? null : $item['guarantee'],
                        'life'                => $item['life'] === 0 ? null : $item['life'],
                        'cover_mark'          => $item['cover_mark'] === '' ? null : $item['cover_mark'],
                        'model_mark'          => $item['model_mark'] === '' ? null : $item['model_mark'],

                        'model_manufacture_group_id' => $item['model_manufacture_group_id'],

                        'owner'          => $item['owner'] === '' ? null : $item['owner'],
                        'lamit'          => strtolower($item['lamit']) === 'да',
                        'sewing_machine' => $item['sewing_machine'] === '' ? null : $item['sewing_machine'],
                        'kant'           => $item['kant'] === '' ? null : $item['kant'],
                        'tkch'           => $item['tkch'] === '' ? null : $item['tkch'],
                        'pack_density'   => $item['pack_density'] === 0.0 ? null : $item['pack_density'],
                        'side_height'    => $item['side_height'] === '' ? null : $item['side_height'],
                        'pack_weight_rb' => $item['pack_weight_rb'] === 0.0 ? null : $item['pack_weight_rb'],
                        'pack_weight_ex' => $item['pack_weight_ex'] === 0.0 ? null : $item['pack_weight_ex'],

                        'model_manufacture_type_code_1c' => $item['model_manufacture_type_code_1c'],
                        // 'model_manufacture_type_name' => $item['model_manufacture_type_name'],

                        'weight'  => $item['weight'] === 0.0 ? null : $item['weight'],
                        'barcode' => $item['barcode'] === '' ? null : $item['barcode'],
                    ],
                );
            }


            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }

        // $data = $request->all();

        // return 'uploaded...';

    }


    //
    public function model($code1C)
    {
        return new ModelResource(Model::query()->find($code1C));
    }

    public function getModels()
    {

        $models = Model::query()
            ->with(['constructs', 'constructs.constructItems'])
            ->get();

        return $models;

        //        return 11111;
        //        return new ModelCollection(Model::all());

        // return new ModelCollection(
        //     Model::query()
        //         ->orderBy('type')
        //         ->orderBy('name')
        //         ->with('collection')
        //         ->get()
        // );

        //        $models = $users = DB::table('models')
        //            ->select(DB::raw('*'))
        //            ->where('type', '<>', '')
        //            ->groupBy('serial')
        //            ->get();

        //        $models = $users = DB::table('models')
        //            ->select('*', 'name', 'type')
        ////            ->where('type', '<>', '')
        //            ->orderBy('type')
        //            ->get();
        //
        //        $models = $models->orderBy('type');

        //        return $models;
        //        return new ModelCollection(DB::table('models')->get());
        //        return new ModelCollection($models);
    }

    public function show(string $id)
    {
        return new ModelResource(Model::query()->find($id));
    }

    public function all()
    {
        return new ModelCollection(Model::all());
    }


    public function modelsLoad(VegasDataGetContract $getter)
    {
        try {
            App::make(CollectionsService::class, [$getter])->updateData();
            App::make(ModelsService::class, [$getter])->updateData();
            return '';

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

}
