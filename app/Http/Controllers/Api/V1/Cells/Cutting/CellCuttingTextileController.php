<?php

namespace App\Http\Controllers\Api\V1\Cells\Cutting;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Cutting\Textiles\CuttingTextileResource;
use App\Models\Manufacture\Cells\Cutting\CuttingTextile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class CellCuttingTextileController extends Controller
{
    /**
     * ___ Получаем все Ткани Раскроя
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingTextiles()
    {
        try {
            $cuttingTextiles = CuttingTextile::all();

            return CuttingTextileResource::collection($cuttingTextiles);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Получаем Ткань Раскроя по коду из 1С
     * @param string $code_1c
     * @return CuttingTextileResource|string
     */
    public function getCuttingTextile(string $code_1c)
    {
        try {
            $validator = Validator::make(
                [
                    'code_1c' => $code_1c,
                ],
                [
                    'code_1c' => 'required|string|exists:cutting_textiles,code_1c'
                ]
            );
            $validated = $validator->validate();

            $cuttingTextile = CuttingTextile::query()->findOrFail($validated['code_1c']);

            return new CuttingTextileResource($cuttingTextile);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем Ткань Настилов
     * @param Request $request
     * @return string
     */
    public function deleteCuttingTextile(Request $request)
    {
        try {
            $validated = $request->validate([
                'code_1c' => 'required|string|exists:cutting_textiles,code_1c'
            ]);

            $cuttingTextile = CuttingTextile::query()->where('code_1c', $validated['code_1c'])->firstOrFail();
            $cuttingTextile->delete();

            return EndPointStaticRequestAnswer::ok('Успешно удалено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создаем Блок
     * @param Request $request
     * @return string
     */
    public function createCuttingTextile(Request $request)
    {
        try {
            //$all = $request->all();

            $data = $request->validate([
                'code_1c'     => 'required|string|exists:cutting_textiles,code_1c',
                'name'        => 'required|string',
                'description' => 'present|nullable|string',
                'active'      => 'required|boolean',
                'layers'      => 'required|integer',
                'width'       => 'required|integer',
                'width_work'  => 'required|integer',
            ]);

            $cuttingTextile = CuttingTextile::query()->create([
                'code_1c'     => $data['code_1c'],
                'name'        => $data['name'],
                'description' => $data['description'],
                'active'      => $data['active'],
                'width'       => $data['width'],
                'width_work'  => $data['width_work'],
                'layers'      => $data['layers'],
            ]);

            if (!$cuttingTextile) {
                throw new Exception('Error creating cutting textile');
            }

            return EndPointStaticRequestAnswer::ok('Сохранено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем Ткань Настилов
     * @param Request $request
     * @return string
     */
    public function updateCuttingTextile(Request $request)
    {
        try {
            //$all = $request->all();

            $data = $request->validate([
                'code_1c'     => 'required|string|exists:cutting_textiles,code_1c',
                'name'        => 'required|string',
                'description' => 'present|nullable|string',
                'active'      => 'required|boolean',
                'layers'      => 'required|integer',
                'width'       => 'required|integer',
                'width_work'  => 'required|integer',
            ]);

            $cuttingTextile = CuttingTextile::query()->where('code_1c', $data['code_1c'])->firstOrFail();

            $updates = $request->only([
                'code_1c',
                'name',
                'description',
                'active',
                'layers',
                'width',
                'width_work',
            ]);

            $cuttingTextile->update($updates);

            return EndPointStaticRequestAnswer::ok('Успешно обновлено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
