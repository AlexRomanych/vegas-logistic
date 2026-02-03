<?php

namespace App\Http\Controllers\Api\V1\Cells\Sewing;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Sewing\Operations\SewingOperationSchemaResource;
use App\Models\Manufacture\Cells\Sewing\SewingOperationSchema;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class CellSewingOperationSchemaController extends Controller
{
    /**
     * ___ Получаем Типовые операции по швейке
     * @return AnonymousResourceCollection|string
     */
    public function getSewingOperationSchemas()
    {
        try {
            $sewingOperations = SewingOperationSchema::query()
                ->with('operations')
                ->get();
            return SewingOperationSchemaResource::collection($sewingOperations);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем Типовую операцию по швейке
     * @param  string  $id
     * @return SewingOperationSchemaResource|string
     */
    public function getSewingOperationSchema(string $id)
    {
        try {
            $validator = Validator::make(['id' => $id], ['id' => 'required|numeric|exists:sewing_operation_schemas,id']);
            $validator->validate(); // __Пробрасываем исключение

            $sewingOperationSchema = SewingOperationSchema::query()->findOrFail($id);
            return new SewingOperationSchemaResource($sewingOperationSchema);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем операцию (pivot) из Схемы
     * @param  Request  $request
     * @return string
     */
    public function deleteSewingOperationFromSchema(Request $request)
    {
        try {
            $data = $request->validate([
                'operation_id'    => 'required|integer|exists:sewing_operations,id',
                'target_id'       => 'required|integer|exists:sewing_operation_schemas,id',
                // Проверяем сам pivot: он может отсутствовать или быть null
                'pivot'           => 'nullable|array',

            ]);

            $schema = SewingOperationSchema::query()->find($data['target_id']);
            $schema->operations()->detach($data['operation_id']);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Добавляем или обновляем операцию (pivot) в Схеме
     * @param  Request  $request
     * @return string
     */
    public function addSewingOperationToSchema(Request $request)
    {
        try {

            $data = $request->validate([
                'operation_id'    => 'required|integer|exists:sewing_operations,id',
                'target_id'       => 'required|integer|exists:sewing_operation_schemas,id',

                // Проверяем сам pivot: он может отсутствовать или быть null
                'pivot'           => 'nullable|array',

                // Если pivot — это массив, проверяем его внутренности
                'pivot.ratio'     => 'exclude_if:pivot,null|nullable|numeric',
                'pivot.amount'    => 'exclude_if:pivot,null|nullable|integer',
                'pivot.condition' => 'exclude_if:pivot,null|nullable|string',
                'pivot.position'  => 'exclude_if:pivot,null|nullable|integer',
            ]);

            $schema = SewingOperationSchema::query()->find($data['target_id']);
            $schema->operations()->syncWithoutDetaching([
                $data['operation_id'] => [
                    'ratio'     => $data['pivot']['ratio'],
                    'amount'    => $data['pivot']['amount'],
                    'condition' => $data['pivot']['condition'],
                    'position'  => $data['pivot']['position'],
                ]
            ]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
