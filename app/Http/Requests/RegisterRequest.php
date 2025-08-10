<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permitir que qualquer usuário faça registro
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{L}\s]+$/u'
            ],
            'surname' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{L}\s]+$/u'
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-\(\)]+$/',
                'unique:users,phone_number'
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'max:60'
            ],
            'terms' => [
                'required',
                'accepted'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'O nome pode conter apenas letras, espaços.',
            'surname.regex' => 'O sobrenome pode conter apenas letras, espaços.',
            'phone_number.regex' => 'O número de telefone deve estar em um formato válido.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'terms.required' => 'Você deve aceitar os termos e condições.',
            'email.unique' => 'Este email já está em uso.',
            'phone_number.unique' => 'Este número de telefone já está em uso.'
        ];
    }


    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'surname' => 'sobrenome',
            'phone_number' => 'número de telefone',
            'terms' => 'termos e condições'
        ];
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
            'phone_number' => $this->phone_number ? preg_replace('/[^0-9+]/', '', $this->phone_number) : null,
            'name' => $this->name ? trim($this->name) : null,
            'surname' => $this->surname ? trim($this->surname) : null,
        ]);
    }
}
