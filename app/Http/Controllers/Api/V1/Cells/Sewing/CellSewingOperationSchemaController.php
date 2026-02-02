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
}
