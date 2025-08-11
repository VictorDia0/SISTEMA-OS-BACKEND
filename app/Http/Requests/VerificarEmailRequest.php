<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerificarEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::exists('users', 'email')],
            'token' => 'sometimes|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email é inválido.',
            'email.exists' => 'O email não está cadastrado.',
            'token.required' => 'O campo token é obrigatório.',
        ];
    }
}
