<?php

namespace App\Http\Controllers\Api\V1\Cells\Cutting;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Cutting\Operations\CuttingOperationResource;
use App\Models\Manufacture\Cells\Cutting\CuttingOperation;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CellCuttingOperationController extends Controller
{

    /**
     * ___ Получаем Типовые операции по швейке
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingOperations()
    {
        try {
            $cuttingOperations = CuttingOperation::all();
            return CuttingOperationResource::collection($cuttingOperations);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем Типовую операцию по швейке
     * @param string $id
     * @return CuttingOperationResource|string
     */
    public function getCuttingOperation(string $id)
    {
        try {
            $validator = Validator::make(['id' => $id], ['id' => 'required|numeric|exists:cutting_operations,id']);
            $validator->validate(); // __Пробрасываем исключение

            $cuttingOperation = CuttingOperation::query()->findOrFail($id);
            return new CuttingOperationResource($cuttingOperation);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создание Типовой операции по швейке
     * @param Request $request
     * @return string
     */
    public function createCuttingOperation(Request $request)
    {
        try {
            $all = $request->all();

            $data = $request->validate([
                'name'        => 'required|unique:cutting_operations',
                'machine'     => 'required|string',
                'type'        => 'required|in:dynamic,static',
                'time'        => 'required|integer',
                'active'      => 'required|boolean',
                'description' => 'required|string',
                'detail'      => 'required|string|in:' . CuttingOperation::DETAIL_COVER . ',' . CuttingOperation::DETAIL_DETAIL,
                'table'       => 'required|string',
                'cover_type'  => 'required|string',
            ]);


            $cuttingOperation = CuttingOperation::query()->create([
                'name'        => $data['name'],
                'machine'     => $data['machine'],
                'type'        => $data['type'],
                'time'        => $data['time'],
                'active'      => $data['active'],
                'description' => $data['description'],
                'detail'      => $data['detail'],
                'table'       => $data['table'],
                'cover_type'  => $data['cover_type'],

            ]);

            if (!$cuttingOperation) {
                throw new Exception('Error creating cutting operation');
            }

            return EndPointStaticRequestAnswer::ok('Сохранено');
            // return new CuttingOperationResource($cuttingOperation);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Обновление Типовой операции по швейке
     * @param Request $request
     * @return string
     */
    public function updateCuttingOperation(Request $request)
    {
        try {
            $all = $request->all();

            $data = $request->validate([
                'id'          => 'required|numeric|exists:cutting_operations,id',
                'name'        => 'string',
                // 'name'        =>'unique:cutting_operations,name,machine,' . $request->id, // Игнорируем текущий ID',
                'machine'     => 'nullable|string',
                'type'        => 'in:dynamic,static',
                'time'        => 'integer',
                'active'      => 'boolean',
                'description' => 'nullable|string',
                'detail'      => 'required|string|in:' . CuttingOperation::DETAIL_COVER . ',' . CuttingOperation::DETAIL_DETAIL,
                'table'       => 'required|string',
                'cover_type'  => 'required|string',
            ]);

            $cuttingOperation = CuttingOperation::query()->findOrFail($data['id']);

            // __ Оставляем все поля, которые БЫЛИ в запросе, включая пустые/null
            $updates = $request->only(['name', 'machine', 'type', 'time', 'active', 'description', 'detail', 'table', 'cover_type']);

            // __ Проверяем, было ли поле в запросе изначально, даже если оно стало null
            // $updates = collect($data)
            //     ->except('id')
            //     ->filter(fn($value, $key) => $request->has($key))
            //     ->toArray();

            // __ Фильтруем данные: оставляем только те, что есть в запросе и не null
            // $updates = collect($data)->except('id')->filter(fn($value) => !is_null($value))->toArray();

            // __ Обновляем именно найденную модель
            $cuttingOperation->update($updates);

            return EndPointStaticRequestAnswer::ok('Сохранено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
