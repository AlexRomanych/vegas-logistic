<?php

namespace App\Http\Controllers\Api\V1\Materials;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Models\Materials\Material;
use App\Models\Materials\MaterialCategory;
use App\Models\Materials\MaterialGroup;
use Exception;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function getMaterials()
    {

    }

    public function materialsUpload(Request $request)
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
