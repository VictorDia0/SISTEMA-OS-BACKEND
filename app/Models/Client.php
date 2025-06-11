<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'cpf',
        'nome_completo',
        'contato',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
    ];

    /**
     * Retorna se o cliente possui e-mail verificado (caso você adicione essa funcionalidade depois).
     */
    public function hasVerifiedEmail(): bool
    {
        // Retorna falso por padrão, ou ajuste conforme seu sistema
        return false;
    }
}
