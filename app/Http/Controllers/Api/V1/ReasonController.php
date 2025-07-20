<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reason\CellItemResource;
use App\Http\Resources\Reason\ReasonResource;
use App\Models\Manufacture\CellsGroup;
use App\Models\Manufacture\Reasons\Reason;
use App\Services\ReasonsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

//use App\Http\Resources\Reason\ReasonResource;
//use Illuminate\Http\Request;

class ReasonController extends Controller
{
    /**
     * ___ Получаем список причин
     * @return AnonymousResourceCollection|string
     */
    public function reasons()
    {
        try {
            return CellItemResource::collection(CellsGroup::query()->with('reasonCategories.reasons')->get());
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем причину по id
     * @param Reason $reason
     * @return ReasonResource|string
     */
    public function reason(Reason $reason)
    {
        try {
            return new ReasonResource($reason);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создаем новую Причину
     * @param Request $request
     * @return string
     */
    public function store(Request $request)
    {
        try {
            // 🌟 Валидация данных прямо в методе контроллера 🌟
            $validatedData = $request->validate([
                'data' => 'required|array',
                'data.name' => 'required|string|min:10|unique:reasons,name',
                'data.display_name' => 'nullable|string',
                'data.description' => 'nullable|string',
                'data.active' => 'boolean',
                'data.reason_category_id' => 'required|integer',
                'data.reason_number_in_reason_category' => 'required|integer',
            ], [
                // Опционально: кастомные сообщения для валидации
                'data.name.required' => 'Название причины обязательно.',
                'data.name.min' => 'Слишком короткое имя.',
                'data.name.unique' => 'Имя уже существует.',
            ]);

            $data = $validatedData['data'];

            // Получаем все номера причин в категории
            $orderNumbers = Reason::query()
                ->where('reason_category_id', $data['reason_category_id'])
                ->pluck('reason_number_in_reason_category')
                ->toArray();

            $firstFreeNumber = ReasonsService::findMinMissingNumber($orderNumbers);

            // 🚀 Создание новой модели с использованием валидированных данных 🚀
            $reason = Reason::query()->create([
                'name' => $data['name'],
                'display_name' => $data['name'],
                'description' => $data['description'],
                'active' => $data['active'],
                'reason_category_id' => $data['reason_category_id'],
                'reason_number_in_reason_category' => $firstFreeNumber,
            ]);

            //            $product = Reason::query()->create($validatedData);

            // ✨ Возвращаем успешный ответ ✨
            //            return response()->json([
            //                'message' => 'Продукт успешно создан!',
            //                'product' => $product
            //            ], 201); // 201 Created - стандартный код для успешного создания ресурса
            //            return new ReasonResource($reason);
            return EndPointStaticRequestAnswer::ok();
        } catch (ValidationException $e) {
            // ❌ Обработка ошибок валидации ❌
            // Laravel автоматически преобразует ValidationException в HTTP 422 Unprocessable Entity
            // если это API-запрос, но явная обработка дает больше контроля.
            //            return response()->json([
            //                'message' => 'Ошибка валидации данных.',
            //                'errors' => $e->errors()
            //            ], 422);
            return EndPointStaticRequestAnswer::fail($e);
        } catch (Exception $e) {
            // 💔 Обработка других неожиданных ошибок 💔
            //            return response()->json([
            //                'message' => 'Произошла ошибка при создании продукта: ' . $e->getMessage()
            //            ], 500); // 500 Internal Server Error
            return EndPointStaticRequestAnswer::fail($e);
        }

    }


    public function update(Request $request, Reason $reason)
    {
        try {
            // 🌟 Валидация данных прямо в методе контроллера 🌟
            $validatedData = $request->validate([
                'data' => 'required|array',
                'data.name' => 'required|string|min:10|unique:reasons,name',
                'data.display_name' => 'nullable|string',
                'data.description' => 'nullable|string',
                'data.active' => 'boolean',
                'data.reason_category_id' => 'required|integer',
                'data.reason_number_in_reason_category' => 'required|integer',
            ], [
                // Опционально: кастомные сообщения для валидации
                'data.name.required' => 'Название причины обязательно.',
                'data.name.min' => 'Слишком короткое имя.',
                'data.name.unique' => 'Имя уже существует.',
            ]);

            $data = $validatedData['data'];

            $reason->update([
                'name' => $data['name'],
                'display_name' => $data['name'],
                'description' => $data['description'],
                'active' => $data['active'],
                //                'reason_category_id' => $data['reason_category_id'],
                //                'reason_number_in_reason_category' => $data['reason_category_id'],
            ]);

            //            // ✨ Возвращаем успешный ответ ✨
            //            return response()->json([
            //                'message' => 'Продукт успешно обновлен!',
            //                'product' => $product
            //            ], 200); // 200 OK - стандартный код для успешного обновления ресурса

            return EndPointStaticRequestAnswer::ok();
        } catch (ValidationException $e) {
            // ❌ Обработка ошибок валидации ❌
            // Laravel автоматически преобразует ValidationException в HTTP 422 Unprocessable Entity
            // если это API-запрос, но явная обработка дает больше контроля.
            //            return response()->json([
            //                'message' => 'Ошибка валидации данных.',
            //                'errors' => $e->errors()
            //            ], 422);
            return EndPointStaticRequestAnswer::fail($e);
        } catch (Exception $e) {
            // 💔 Обработка других неожиданных ошибок 💔
            //            return response()->json([
            //                'message' => 'Произошла ошибка при создании продукта: ' . $e->getMessage()
            //            ], 500); // 500 Internal Server Error
            return EndPointStaticRequestAnswer::fail($e);
        }


    }

    /**
     * ___ Удаляем причину
     * @param Reason $reason
     * @return string
     */
    public
    function delete(Reason $reason)
    {
        try {
            $reason->delete();
            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            // Обработка ошибок при удалении
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем список причин по Группе ПЯ и категории
     * @param Request $request
     * @param string|null $cellsGroupId ID группы ячеек (из маршрута, может быть null если отсутствует)
     * @param string|null $reasonsCategoryId ID категории причин (из маршрута, может быть null если отсутствует)
     * @return JsonResponse|AnonymousResourceCollection|string
     */
    public function getReasonsByCellsGroupAndReasonCategory(
        Request $request,
        string  $cellsGroupId = null,
        string  $reasonsCategoryId = null
    )
    {
        // return ['answer' => [
        // 'cellsGroupId' => $cellsGroupId,
        //  'reasonsCategoryId' => $reasonsCategoryId,
        // ]];


        try {
            // --- 1. Ручная валидация входных параметров ---
            // Используем фасад Validator для создания валидатора вручную,
            // чтобы мы могли перехватить результат вместо автоматического выброса исключения.
            $validator = Validator::make([
                'cellsGroupId' => $cellsGroupId,
                'reasonsCategoryId' => $reasonsCategoryId,
            ], [
                'cellsGroupId' => [
                    'required',     // Параметр обязателен
                    'integer',      // Должен быть целым числом
                    'min:1',        // Должен быть положительным числом
                ],
                'reasonsCategoryId' => [
                    'required',
                    'integer',
                    'min:1',
                ],
            ]);

            // Если валидация по базовым правилам (тип, обязательность, min) не прошла
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid parameters provided.',
                    'data' => [], // Возвращаем пустой массив данных
                    'errors' => $validator->errors(), // Можно также вернуть детали ошибок, если нужно для отладки
                ], 200); // Возвращаем 200 OK, но с пустыми данными
            }

            // --- 2. Проверка существования записей в базе данных ---
            // Теперь, когда мы знаем, что cellsGroupId и reasonsCategoryId - это валидные целые числа,
            // ищем их в базе данных.
            $reasons = Reason::query()->whereHas('reasonCategory', function ($query) use ($reasonsCategoryId, $cellsGroupId) {
                // 1. Фильтруем категории по их собственному ID
                $query->where('id', $reasonsCategoryId)
                    // 2. И внутри этой категории, фильтруем по связанной группе
                    ->whereHas('cellsGroup', function ($subQuery) use ($cellsGroupId) {
                        $subQuery->where('id', $cellsGroupId);
                    });
            })->get();

            // Если ни одна запись не найдена, возвращаем пустой массив
            if ($reasons->isEmpty()) {
                return response()->json([
                    'message' => 'One or both entities not found in the database.',
                    'data' => [], // Возвращаем пустой массив данных
                ], 200); // Возвращаем 200 OK, но с пустыми данными
            }

            return ReasonResource::collection($reasons);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }

    }


    /**
     * Получает причины по ID группы ячеек и ID категории причин.
     * /*
     * public function getReasonsByCellsGroupAndReasonCategory(Request $request, $cellsGroupId, $reasonsCategoryId)
     * {
     * // --- 1. Валидация входных параметров ---
     * // Используем встроенную валидацию Laravel для проверки типов и существования в БД.
     * $request->validate([
     * 'cellsGroupId' => [
     * 'required',     // Параметр обязателен
     * 'integer',      // Должен быть целым числом
     * 'min:1',        // Должен быть положительным числом
     * Rule::exists('cells_groups', 'id'), // Проверяем, существует ли этот ID в таблице 'cells_groups'
     * ],
     * 'reasonsCategoryId' => [
     * 'required',
     * 'integer',
     * 'min:1',
     * Rule::exists('reason_categories', 'id'), // Проверяем, существует ли этот ID в таблице 'reason_categories'
     * ],
     * ]);
     *
     * // Если валидация не прошла, Laravel автоматически выбросит исключение
     * // и вернет JSON-ответ с ошибками 422 Unprocessable Entity.
     * // Дальнейший код выполнится только при успешной валидации.
     *
     * // --- 2. Поиск сущностей по проверенным ID ---
     * // Теперь мы можем быть уверены, что cellsGroupId и reasonsCategoryId существуют в БД.
     * $cellsGroup = CellsGroup::find($cellsGroupId);
     * $reasonCategory = ReasonCategory::find($reasonsCategoryId);
     *
     * // Дополнительные проверки (хотя Rule::exists уже гарантирует их наличие)
     * // Эти проверки могут быть полезны, если вы не используете Rule::exists
     * // или если вам нужна более специфическая обработка "не найдено".
     * if (!$cellsGroup) {
     * return response()->json([
     * 'message' => 'Cells Group not found.' // Группа ячеек не найдена (несмотря на Rule::exists, если логика сложнее)
     * ], 404);
     * }
     *
     * if (!$reasonCategory) {
     * return response()->json([
     * 'message' => 'Reason Category not found.' // Категория причин не найдена
     * ], 404);
     * }
     *
     * // --- 3. Выполнение бизнес-логики ---
     * // Теперь у вас есть $cellsGroup и $reasonCategory, и вы можете использовать их.
     * // Например, найти все причины, связанные с этой группой ячеек и категорией.
     *
     * // Пример: Получаем причины, связанные с обеими сущностями
     * // Предположим, у вас есть отношения в моделях
     * $reasons = $cellsGroup->reasons() // Если CellsGroup имеет отношение hasMany/belongsToMany к Reason
     * ->where('reason_category_id', $reasonsCategoryId)
     * ->get();
     *
     * // Если у вас нет прямых отношений, вы можете искать напрямую в модели Reason:
     * // $reasons = Reason::where('cells_group_id', $cellsGroupId)
     * //                  ->where('reason_category_id', $reasonsCategoryId)
     * //                  ->get();
     *
     * // --- 4. Возврат результата ---
     * // Возвращаем данные в формате JSON.
     * return response()->json([
     * 'message' => 'Reasons retrieved successfully.',
     * 'cells_group' => $cellsGroup,
     * 'reason_category' => $reasonCategory,
     * 'reasons' => $reasons,
     * ]);
     * }
     */


}
