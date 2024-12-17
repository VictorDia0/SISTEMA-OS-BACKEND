<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer essa requisição.
     */
    public function authorize(): bool
    {
        return true; // Permitir sempre (ajuste conforme necessário)
    }

    /**
     * Define as regras de validação para a requisição.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
    }

    /**
     * Mensagens personalizadas para erros de validação.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ];
    }
}
