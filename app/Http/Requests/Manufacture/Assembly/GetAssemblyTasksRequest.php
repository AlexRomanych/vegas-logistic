<?php

namespace App\Http\Requests\Manufacture\Assembly;

use App\Models\Manufacture\Cells\Assembly\AssemblyTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAssemblyTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowedSectors = AssemblyTask::ASSEMBLY_TASK_SECTORS;

        return [
            'id'        => ['nullable', 'integer', 'exists:assembly_tasks,id'],

            // __ Проверяем само поле sectors (если передали одиночную строку)
            'sectors'   => [
                'nullable',
                function ($attribute, $value, $fail) use ($allowedSectors) {
                    if (!is_string($value) && !is_array($value)) {
                        $fail("Поле {$attribute} должно быть строкой или массивом.");
                        return;
                    }

                    // Если передана одиночная строка — сразу валидируем её по списку
                    if (is_string($value) && !in_array($value, $allowedSectors, true)) {
                        $fail("Недопустимое значение участка: '{$value}'.");
                    }
                },
            ],

            // __ Если sectors — это массив, валидируем каждый элемент массива по списку констант
            'sectors.*' => [
                'string',
                Rule::in($allowedSectors),
            ],
            //'period'       => 'nullable|array',
            //'period.start' => 'required_if:period,*,!null|date',        // условная валидация
            //'period.end'   => 'required_if:period,*,!null|date',

            'period'       => ['nullable', 'array'],
            'period.start' => ['required_with:period', 'date'],
            'period.end'   => ['required_with:period', 'date', 'after_or_equal:period.start'],
        ];
    }

    /**
     * Хелпер для получения нормализованного массива участков
     */
    public function getSectors(): ?array
    {
        // 1. Если ключа 'sectors' вообще нет в запросе -> NULL (Все участки)
        if (!$this->has('sectors')) {
            return null;
        }

        $sectors = $this->input('sectors');

        // 2. Если ключ передан как пустая строка ('') или пустой массив -> [] (Без участков)
        if (empty($sectors)) {
            return [];
        }

        // 3. Если передана строка -> ['foam_side']
        if (is_string($sectors)) {
            return [$sectors];
        }

        // 4. Если передан массив -> ['foam_side', 'latex']
        return $sectors;
    }
}
