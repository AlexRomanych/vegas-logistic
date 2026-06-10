<?php

namespace App\Http\Controllers\Api\V1\Cells\Cutting;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Cutting\Procedures\CuttingProcedureResource;
use App\Models\Manufacture\Cells\Cutting\CuttingOperation;
use App\Models\Manufacture\Cells\Cutting\CuttingProcedure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class CellCuttingProcedureController extends Controller
{

    /**
     * ___ Возвращаем Процедуры Раскроя
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingProcedures()
    {
        try {
            $cuttingProcedures = CuttingProcedure::all();
            //$cuttingProcedures = CuttingProcedure::query()
            //    ->where('id', '<>', 0)
            //    ->get();
            return CuttingProcedureResource::collection($cuttingProcedures);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Получаем Процедуру Раскроя по id
     * @param string $id
     * @return CuttingProcedureResource|string
     */
    public function getCuttingProcedure(string $id)
    {
        try {
            $validator = Validator::make(['id' => $id], ['id' => 'required|numeric|exists:cutting_procedures,id']);
            $validator->validate(); // __Пробрасываем исключение

            $cuttingProcedure = CuttingProcedure::query()->findOrFail($id);
            return new CuttingProcedureResource($cuttingProcedure);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создание Процедуры Раскроя
     * @param Request $request
     * @return string
     */
    public function createCuttingProcedure(Request $request)
    {
        try {
            $all = $request->all();

            $data = $request->validate([
                'name'        => 'required|unique:cutting_procedures,name',
                'active'      => 'required|boolean',
                'description' => 'present|nullable|string',
                'object_name' => 'required|string|in:' . CuttingOperation::DETAIL_PANEL . ',' . CuttingOperation::DETAIL_SIDE,
                'text'        => 'required|string',
            ]);

            $cuttingProcedure = CuttingProcedure::query()->create([
                'name'        => $data['name'],
                'active'      => $data['active'],
                'description' => $data['description'],
                'text'        => $data['text'],
                'object_name' => $data['object_name'],

            ]);

            if (!$cuttingProcedure) {
                throw new Exception('Error creating cutting Procedure');
            }

            return EndPointStaticRequestAnswer::ok('Сохранено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновление Процедуры Раскроя
     * @param Request $request
     * @return string
     */
    public function updateCuttingProcedure(Request $request)
    {
        try {
            //$all = $request->all();

            $data = $request->validate([
                'id'          => 'required|integer|exists:cutting_procedures,id',
                'name'        => 'required|string',
                'active'      => 'required|boolean',
                'description' => 'present|nullable|string',
                'object_name' => 'required|string|in:' . CuttingOperation::DETAIL_PANEL . ',' . CuttingOperation::DETAIL_SIDE,
                'text'        => 'required|string',
            ]);

            $cuttingProcedure = CuttingProcedure::query()->findOrFail($data['id']);

            // __ Оставляем все поля, которые БЫЛИ в запросе, включая пустые/null
            $updates = $request->only(['name', 'active', 'description', 'object_name', 'text']);

            // __ Проверяем, было ли поле в запросе изначально, даже если оно стало null
            // $updates = collect($data)
            //     ->except('id')
            //     ->filter(fn($value, $key) => $request->has($key))
            //     ->toArray();

            // __ Фильтруем данные: оставляем только те, что есть в запросе и не null
            // $updates = collect($data)->except('id')->filter(fn($value) => !is_null($value))->toArray();

            // __ Обновляем именно найденную модель
            $cuttingProcedure->update($updates);

            return EndPointStaticRequestAnswer::ok('Сохранено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
