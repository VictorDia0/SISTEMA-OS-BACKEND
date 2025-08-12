<?php

namespace App\Http\Requests\API;

use App\Exceptions\AuthException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

class RedefinirSenhaRequest extends FormRequest
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
            'password' => 'sometimes|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email é inválido.',
            'email.exists' => 'O e-mail informado não está cadastrado na base de dados!',
            'password.confirmed' => 'As senhas devem ser iguais.',
            'password.min' => 'O campo password deve ter mais de 8 caracteres.',
        ];
    }

    public function failedValidation(Validator $validator): never
    {
        $code = Response::HTTP_UNPROCESSABLE_ENTITY;

        if (
            $validator->errors()->has('email') &&
            $validator->errors()->first('email') === $this->messages()['email.exists']
        ) {
            $code = Response::HTTP_NOT_FOUND;
        }

        throw new AuthException($validator->errors()->first(), $code);
    }
}
