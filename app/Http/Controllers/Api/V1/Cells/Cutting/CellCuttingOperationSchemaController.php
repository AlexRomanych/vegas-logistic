<?php

namespace App\Http\Controllers\Api\V1\Cells\Cutting;

use App\Classes\EndPointStaticRequestAnswer;
use App\Classes\CuttingTimeLabor;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Cutting\Operations\CuttingOperationSchemaResource;
use App\Models\Manufacture\Cells\Cutting\CuttingOperationSchema;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class CellCuttingOperationSchemaController extends Controller
{
    /**
     * ___ Получаем Типовые операции по швейке
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingOperationSchemas()
    {
        try {
            $cuttingOperations = CuttingOperationSchema::query()
                ->with('operations')
                ->get();
            return CuttingOperationSchemaResource::collection($cuttingOperations);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем Типовую операцию по швейке
     * @param string $id
     * @return CuttingOperationSchemaResource|string
     */
    public function getCuttingOperationSchema(string $id)
    {
        try {
            $validator = Validator::make(['id' => $id], ['id' => 'required|numeric|exists:cutting_operation_schemas,id']);
            $validator->validate(); // __Пробрасываем исключение

            $cuttingOperationSchema = CuttingOperationSchema::query()->findOrFail($id);
            return new CuttingOperationSchemaResource($cuttingOperationSchema);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создаем Схему Типовых операций по швейке
     * @param Request $request
     * @return CuttingOperationSchemaResource|string
     */
    public function createCuttingOperationSchema(Request $request)
    {
        try {
            $data = $request->validate([
                'name'        => 'required|string|unique:cutting_operation_schemas,name', // __ Нет еще в базе
                'description' => 'nullable|string'
            ], [
                'name.unique' => 'Ошибка! Схема ":input" уже существует.',
            ]);

            $newCuttingOperationSchema = CuttingOperationSchema::query()->create($data);

            return new CuttingOperationSchemaResource($newCuttingOperationSchema);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем Схему Типовых операций по швейке
     * @param Request $request
     * @return string
     */
    public function updateCuttingOperationSchema(Request $request)
    {
        try {
            $data = $request->validate([
                'data'             => 'required|array',
                'data.id'          => 'required|integer|exists:cutting_operation_schemas,id',
                'data.name'        => 'required|string', // __ Нет еще в базе
                'data.description' => 'nullable|string'
            ]);

            $cuttingOperationSchema = CuttingOperationSchema::query()->findOrFail($data['data']['id']);
            $sameNameSchema = CuttingOperationSchema::query()->where('name', $data['data']['name'])->first();

            if ($sameNameSchema) {
                if ($sameNameSchema->id !== $cuttingOperationSchema->id) {
                    throw new Exception('Ошибка! Схема "' . $data['data']['name'] . '" уже существует.');
                }
            }

            $cuttingOperationSchema->name = $data['data']['name'];
            if (isset($data['data']['description'])) {
                $cuttingOperationSchema->description = $data['data']['description'];
            }

            $cuttingOperationSchema->save();
            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем операцию (pivot) из Схемы
     * @param Request $request
     * @return string
     */
    public function deleteCuttingOperationFromSchema(Request $request)
    {
        try {
            $data = $request->validate([
                'operation_id' => 'required|integer|exists:cutting_operations,id',
                'target_id'    => 'required|integer|exists:cutting_operation_schemas,id',
                // Проверяем сам pivot: он может отсутствовать или быть null
                'pivot'        => 'nullable|array',

            ]);

            $schema = CuttingOperationSchema::query()->find($data['target_id']);
            $schema->operations()->detach($data['operation_id']);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Проверяем Схему на время
     * @param string $id
     * @return array[]|string
     */
    public function checkCuttingOperationSchemaForSummaryTime(string $id)
    {
        try {
            $validator = Validator::make([
                'id' => $id
            ], [
                'id' => 'required|numeric|exists:cutting_operation_schemas,id'
            ]);

            $data = $validator->validate();
            $schema = CuttingOperationSchema::query()->find($data['id']);

            if (!$schema) {
                throw new Exception('Схема с id = ' . $id . ' не найдена');
            }

            $SIZES = [
                '60x200x20',
                '70x200x20',
                '80x200x20',
                '90x200x20',
                '100x200x20',
                '120x200x20',
                '140x200x20',
                '160x200x20',
                '180x200x20',
                '200x200x20',
            ];

            $timeObject = new CuttingTimeLabor();

            $result = [];
            foreach ($SIZES as $size) {
                $result[] = [mb_substr($size, 0, -3) => $timeObject->getTimeLaborBySizeAndCuttingSchema($size, $schema)];
            }

            return ['data' => $result];
            //return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Добавляем или обновляем операцию (pivot) в Схеме
     * @param Request $request
     * @return string
     */
    public function addCuttingOperationToSchema(Request $request)
    {
        try {
            $data = $request->validate([
                'operation_id'    => 'required|integer|exists:cutting_operations,id',
                'target_id'       => 'required|integer|exists:cutting_operation_schemas,id',

                // Проверяем сам pivot: он может отсутствовать или быть null
                'pivot'           => 'nullable|array',

                // Если pivot — это массив, проверяем его внутренности
                'pivot.ratio'     => 'exclude_if:pivot,null|nullable|numeric',
                'pivot.amount'    => 'exclude_if:pivot,null|nullable|integer',
                'pivot.condition' => 'exclude_if:pivot,null|nullable|string',
                'pivot.position'  => 'exclude_if:pivot,null|nullable|integer',
            ]);

            $schema = CuttingOperationSchema::query()->find($data['target_id']);
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
