<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Manipula falhas de validação e retorna uma resposta JSON.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Falha na validação dos dados',
                'errors' => $validator->errors(),
            ], 422)
        );
        // O código de status HTTP 422 significa "Unprocessable Entity" (Entidade Não Processável).
        //Esse código é usado quando o servidor entende a requisição do cliente, mas não pode processá-la devido a erros de validação no lado do servidor.
    }

    /**
     * Retorna as regras de validação para os dados do usuario.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => 'sometimes|string|max:50',
            'surname' => 'sometimes|string|max:50',
            'phone_number' => 'nullable|string|max:20', // Consistência no nome.
            'email' => 'sometimes|email|max:255|unique:users,email,' . $userId,
            'password' => $this->isMethod('patch') || $this->isMethod('put')
                ? 'nullable|string|min:8'
                : 'required|string|min:8',
        ];
    }

    /**
     * Retorna as mensagens de erro personalizadas para as regras de validação.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.max' => 'O campo nome pode ter no máximo :max caracteres.',
            'surname.required' => 'O campo sobrenome é obrigatório.',
            'surname.string' => 'O campo sobrenome deve ser uma string.',
            'surname.max' => 'O campo sobrenome pode ter no máximo :max caracteres.',
            'phone_number.max' => 'O número de telefone pode ter no máximo :max caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço válido.',
            'email.unique' => 'O email fornecido já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
        ];
    }
}
