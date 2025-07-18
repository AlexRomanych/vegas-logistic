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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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

}
