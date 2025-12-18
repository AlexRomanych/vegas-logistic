<?php

namespace App\Http\Controllers\Api\V1\Materials;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Models\Materials\Material;
use App\Models\Materials\MaterialCategory;
use App\Models\Materials\MaterialGroup;
use App\Services\MaterialsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{

    public function getMaterials()
    {
        $materials = Material::all();

        // Получаем только Группы (у которых нет родительской группы и категории)
        $materials = Material::query()
            ->whereNull('material_group_code_1c')
            ->whereNull('material_category_code_1c')
            ->with('categories.materials') // Подгружаем категории и их материалы
            ->orderBy('name')
            ->get();

        return $materials;
    }

    /**
     * ___ Materials upload
     * @param Request $request
     * @return string
     */
    public function materialsUpload(Request $request)
    {
        try {
            $data = $request->validate(['data' => 'required|json']);

            // DB::table('materials')->truncate();

            $data = json_decode($data['data'], true);

            $CODE_1C_COPY = CODE_1C . '_copy';
            foreach ($data as $materialGroup) {

                // __ Вставляем Группу материалов
                $createdMaterialGroup = Material::query()->updateOrCreate(
                    [
                        CODE_1C => $materialGroup[CODE_1C],
                    ],
                    [
                        'name'                      => $materialGroup['name'],
                        'material_group_code_1c'    => null,
                        'material_category_code_1c' => null,
                        $CODE_1C_COPY               => $materialGroup[CODE_1C],
                    ],
                );

                foreach ($materialGroup['categories'] as $materialCategory) {

                    // __ Вставляем Категорию материалов
                    $createdMaterialCategory = Material::query()->updateOrCreate(
                        [
                            CODE_1C => $materialCategory[CODE_1C],
                        ],
                        [
                            'material_group_code_1c'    => $materialGroup[CODE_1C],
                            'material_category_code_1c' => null,
                            'name'                      => $materialCategory['name'],
                            $CODE_1C_COPY               => $materialCategory[CODE_1C],
                        ],
                    );

                    foreach ($materialCategory['materials'] as $material) {

                        // __ Вставляем Материалы
                        $createdMaterial = Material::query()->updateOrCreate(
                            [
                                CODE_1C => $material[CODE_1C],
                            ],
                            [
                                'material_category_code_1c' => $materialCategory[CODE_1C],
                                'name'                      => $material['name'],
                                'unit'                      => MaterialsService::getCorrectUnitName($material['unit']),
                                'supplier'                  => $material['supplier'],
                                $CODE_1C_COPY               => $material[CODE_1C],
                            ],
                        );
                    }

                }


            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }


    }


    public function materialsUpload_Old(Request $request)
    {
        try {
            $data = $request->validate(['data' => 'required|json']);

            $data = json_decode($data['data'], true);

            foreach ($data as $materialGroup) {

                // __ Вставляем Группу материалов
                $createdMaterialGroup = MaterialGroup::query()->updateOrCreate(
                    [
                        CODE_1C => $materialGroup[CODE_1C],
                    ],
                    [
                        'name' => $materialGroup['name'],
                    ],
                );

                foreach ($materialGroup['categories'] as $materialCategory) {

                    // __ Вставляем Категорию материалов
                    $createdMaterialCategory = MaterialCategory::query()->updateOrCreate(
                        [
                            CODE_1C => $materialCategory[CODE_1C],
                        ],
                        [
                            'material_group_code_1c' => $materialGroup[CODE_1C],
                            'name'                   => $materialCategory['name'],
                        ],
                    );

                    foreach ($materialCategory['materials'] as $material) {

                        // __ Вставляем Материалы
                        $createdMaterial = Material::query()->updateOrCreate(
                            [
                                CODE_1C => $material[CODE_1C],
                            ],
                            [
                                'material_category_code_1c' => $materialCategory[CODE_1C],
                                'name'                      => $material['name'],
                                'unit'                      => $material['unit'],
                                'supplier'                  => $material['supplier'],
                            ],
                        );
                    }

                }


            }

            $a = 0;

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }


    }
}
