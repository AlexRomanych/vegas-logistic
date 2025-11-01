<?php

namespace App\Http\Requests\Clients;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id'          => 'nullable|integer',
            'name'        => 'required|string|min:3|max:255',
            'add_name'    => 'nullable|string|min:3|max:255',
            'short_name'  => 'required|string|min:3|max:255',
            'active'      => 'required|boolean',
            'region'      => 'required|string|in:east,west',
            'description' => 'nullable|string',
            'comment'     => 'nullable|string',

            'country'    => 'nullable|string',
            'address'    => 'nullable|string',
            'longitude'  => 'nullable|numeric',
            'latitude'   => 'nullable|numeric',
            'manager_id' => 'nullable|integer',
            'note'       => 'nullable|string',
            'meta'       => 'nullable|string',
        ];
    }
}
