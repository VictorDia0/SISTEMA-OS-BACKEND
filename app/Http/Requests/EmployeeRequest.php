<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permitir a execução do request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtém o ID do funcionário para a validação no caso de update
        $employeeId = $this->route('id') ?? null;

        return [
            'name' => 'required|string|max:20',
            'surname' => 'required|string|max:100',
            'RG' => 'nullable|string|max:15',
            'CPF' => ['required', 'string', 'size:11', Rule::unique('employees', 'CPF')->ignore($employeeId)],
            'telefone' => 'nullable|string|max:20',
            'celular' => 'required|string|max:20',
            'email' => ['required', 'email', Rule::unique('employees', 'email')->ignore($employeeId)],
            'password' => $this->isMethod('post') ? 'required|string|min:8' : 'nullable|string|min:8',
            'CEP' => 'required|string|size:8',
            'rua' => 'required|string|max:100',
            'numero' => 'required|string|max:10',
            'bairro' => 'required|string|max:50',
            'estado' => 'required|string|size:2',
            'situacao' => 'nullable|in:Ativo,Inativo',
            'permissao' => 'nullable|string|max:255',
        ];
    }

    /**
     * Customize the validation error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'surname.required' => 'O sobrenome é obrigatório.',
            'CPF.required' => 'O CPF é obrigatório.',
            'CPF.unique' => 'O CPF já está em uso.',
            'email.required' => 'O email é obrigatório.',
            'email.unique' => 'O email já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'CEP.required' => 'O CEP é obrigatório.',
            'estado.size' => 'O estado deve ter exatamente 2 caracteres.',
        ];
    }
}
