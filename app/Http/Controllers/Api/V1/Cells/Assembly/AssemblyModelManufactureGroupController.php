<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Assembly\ManufactureGroups\AssemblyModelManufactureGroupResource;
use App\Models\Models\ModelManufactureGroup;
use App\Services\Manufacture\AssemblyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AssemblyModelManufactureGroupController extends Controller
{

    /*
     * ___ Получаем Группы Моделей для Сортировки
     */
    public function getModelManufactureGroups()
    {
        try {
            $modelManufactureGroups = ModelManufactureGroup::all();
            return AssemblyModelManufactureGroupResource::collection($modelManufactureGroups);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     *  ___ Получаем Группы Моделей для Сортировки
     * @return AssemblyModelManufactureGroupResource|string
     */
    public function getModelManufactureGroupById(string $id)
    {
        try {
            $validator = Validator::make(
                [
                    'id' => $id
                ],
                [
                    'id' => 'required|integer|exists:model_manufacture_groups,id'
                ]
            );
            $validated = $validator->validate();

            $modelManufactureGroup = ModelManufactureGroup::query()->findOrFail($validated['id']);
            return new AssemblyModelManufactureGroupResource($modelManufactureGroup);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создаем Группу Моделей для Сортировки
     * @param Request $request
     * @return string
     */
    public function createModelManufactureGroup(Request $request)
    {
        try {
            $data = $request->validate([
                'group'              => 'required|array',
                'group.name'         => 'required|unique:model_manufacture_groups,name',
                'group.group_number' => 'required|unique:model_manufacture_groups,group_number',
                'group.description'  => 'present|nullable|string',
                'group.active'       => 'required|boolean',
            ]);

            $group = $data['group'];

            $modelManufactureGroup = ModelManufactureGroup::query()->create([
                'name'         => $group['name'],
                'description'  => $group['description'],
                'active'       => $group['active'],
                'group_number' => $group['group_number'],
            ]);

            if (!$modelManufactureGroup) {
                throw new Exception('Error creating Model Manufacture Group');
            }

            return EndPointStaticRequestAnswer::ok('Сохранено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем Группу Моделей для Сортировки
     * @param Request $request
     * @return string
     */
    public function updateModelManufactureGroup(Request $request)
    {
        try {
            // __ Извлекаем ID для правил уникальности
            $groupId = $request->input('data.group.id');

            // __ Валидация с учетом исключения текущей записи (ignore)
            $validated = $request->validate([
                'data.group'              => 'required|array',
                'data.group.id'           => 'required|exists:model_manufacture_groups,id',
                'data.group.name'         => [
                    'required',
                    'string',
                    Rule::unique('model_manufacture_groups', 'name')->ignore($groupId),
                ],
                'data.group.group_number' => [
                    'required',
                    'integer',
                    Rule::unique('model_manufacture_groups', 'group_number')->ignore($groupId),
                ],
                'data.group.description'  => 'present|nullable|string',
                'data.group.active'       => 'required|boolean',
            ]);

            // __ Достаем плоский массив полей ['name' => '...', 'group_number' => '...', ...]
            $groupData = $validated['data']['group'];

            // __ Находим и обновляем модель
            $modelManufactureGroup = ModelManufactureGroup::query()->findOrFail($groupData['id']);

            // __ Метод update сам отфильтрует id, если его нет в $fillable модели
            $modelManufactureGroup->update($groupData);

            return EndPointStaticRequestAnswer::ok('Успешно обновлено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем Группу Моделей для Сортировки
     * @param Request $request
     * @return string
     */
    public function deleteModelManufactureGroup(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:model_manufacture_groups,id',
            ]);
            $modelManufactureGroup = ModelManufactureGroup::query()->findOrFail($validated['id']);
            $modelManufactureGroup->delete();

            return EndPointStaticRequestAnswer::ok('Успешно удалено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }

    }


}
