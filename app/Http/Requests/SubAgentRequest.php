<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubAgentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'unique:users,user_name'],
            'name' => ['required', 'string'],
            'password' => 'required|min:6',
            'permissions' => ['required', 'array', 'min:1'],
        ];
    }
}