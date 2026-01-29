<?php

namespace App\Http\Requests\Manufacture\Sewing\Sync;

// use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SyncSewingTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Настройте проверку прав, если нужно
    }

    public function rules(): array
    {
        return [
            // Основной массив различий
            'diffs' => ['required', 'array'],

            // Валидация задачи (ISewingTaskArrayDiff)
            // 'diffs.*.taskId' => ['required'], // Не ставим exists, так как может быть temp_id
            // 'diffs.*.type' => ['nullable', 'string', 'in:CREATED'],
            // 'diffs.*.current' => ['required_if:diffs.*.type,CREATED', 'array'],
            //
            // // Если задача обновляется, проверяем изменения
            // 'diffs.*.taskChanges' => ['nullable', 'array'],
            // 'diffs.*.taskChanges.action_at' => ['nullable', 'array'],
            // 'diffs.*.taskChanges.action_at.old' => ['required_with:diffs.*.taskChanges.action_at', 'string'],
            // 'diffs.*.taskChanges.action_at.new' => ['required_with:diffs.*.taskChanges.action_at', 'string'],
            //
            // 'diffs.*.taskChanges.position' => ['nullable', 'array'],
            // 'diffs.*.taskChanges.position.old' => ['required_with:diffs.*.taskChanges.position', 'integer'],
            // 'diffs.*.taskChanges.position.new' => ['required_with:diffs.*.taskChanges.position', 'integer'],
            //
            // // Валидация строк (ISewingTaskArrayLineDiffs)
            // 'diffs.*.lineChanges' => ['nullable', 'array'],
            // 'diffs.*.lineChanges.*.lineId' => ['required'],
            // 'diffs.*.lineChanges.*.lineIdRef' => ['nullable'],
            // 'diffs.*.lineChanges.*.type' => ['required', 'string', 'in:ADDED,UPDATED,DELETED'],
            //
            // // Валидация полей внутри изменений строки
            // 'diffs.*.lineChanges.*.amount' => ['nullable', 'array'],
            // 'diffs.*.lineChanges.*.amount.new' => ['required_with:diffs.*.lineChanges.*.amount', 'numeric', 'min:0'],
            //
            // 'diffs.*.lineChanges.*.position' => ['nullable', 'array'],
            // 'diffs.*.lineChanges.*.position.new' => ['required_with:diffs.*.lineChanges.*.position', 'integer', 'min:0'],
        ];
    }

    /**
     * Кастомные сообщения об ошибках (опционально)
     */
    public function messages(): array
    {
        return [
            'diffs.*.taskId.required' => 'ID задачи обязателен для синхронизации',
            'diffs.*.current.required_if' => 'Данные новой задачи обязательны при типе CREATED',
        ];
    }
}
