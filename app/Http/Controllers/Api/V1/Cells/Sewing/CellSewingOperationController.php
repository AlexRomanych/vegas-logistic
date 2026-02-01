<?php

namespace App\Http\Controllers\Api\V1\Cells\Sewing;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Sewing\Operations\SewingOperationResource;
use App\Models\Manufacture\Cells\Sewing\SewingOperation;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CellSewingOperationController extends Controller
{

    /**
     * ___ Получаем Типовые операции по швейке
     * @return AnonymousResourceCollection|string
     */
    public function getSewingOperations()
    {
        try {
            $sewingOperations = SewingOperation::all();
            return SewingOperationResource::collection($sewingOperations);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем Типовую операцию по швейке
     * @param  string  $id
     * @return SewingOperationResource|string
     */
    public function getSewingOperation(string $id)
    {
        try {
            $validator = Validator::make(['id' => $id], ['id' => 'required|numeric|exists:sewing_operations,id']);
            $validator->validate(); // __Пробрасываем исключение

            $sewingOperation = SewingOperation::query()->findOrFail($id);
            return new SewingOperationResource($sewingOperation);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создание Типовой операции по швейке
     * @param  Request  $request
     * @return string
     */
    public function createSewingOperation(Request $request)
    {
        try {

            $all = $request->all();

            $data = $request->validate([
                'name'        => 'required|unique:sewing_operations',
                'machine'     => 'required|string',
                'type'        => 'required|in:dynamic,static',
                'time'        => 'required|integer',
                'active'      => 'required|boolean',
                'description' => 'required|string',
            ]);


            $sewingOperation = SewingOperation::query()->create([
                'name'        => $data['name'],
                'machine'     => $data['machine'],
                'type'        => $data['type'],
                'time'        => $data['time'],
                'active'      => $data['active'],
                'description' => $data['description'],

            ]);

            if (!$sewingOperation) {
                throw new Exception('Error creating sewing operation');
            }

            return EndPointStaticRequestAnswer::ok();
            // return new SewingOperationResource($sewingOperation);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Создание Типовой операции по швейке
     * @param  Request  $request
     * @return string
     */
    public function updateSewingOperation(Request $request)
    {
        try {

            $all = $request->all();

            $data = $request->validate([
                'id'          => 'required|numeric|exists:sewing_operations,id',
                'name'        => 'string',
                // 'name'        =>'unique:sewing_operations,name,machine,' . $request->id, // Игнорируем текущий ID',
                'machine'     => 'string',
                'type'        => 'in:dynamic,static',
                'time'        => 'integer',
                'active'      => 'boolean',
                'description' => 'nullable|string',
            ]);

            $sewingOperation = SewingOperation::query()->findOrFail($data['id']);

            // __ Оставляем все поля, которые БЫЛИ в запросе, включая пустые/null
            $updates = $request->only(['name', 'machine', 'type', 'time', 'active', 'description']);

            // __ Проверяем, было ли поле в запросе изначально, даже если оно стало null
            // $updates = collect($data)
            //     ->except('id')
            //     ->filter(fn($value, $key) => $request->has($key))
            //     ->toArray();

            // __ Фильтруем данные: оставляем только те, что есть в запросе и не null
            // $updates = collect($data)->except('id')->filter(fn($value) => !is_null($value))->toArray();

            // __ Обновляем именно найденную модель
            $sewingOperation->update($updates);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
