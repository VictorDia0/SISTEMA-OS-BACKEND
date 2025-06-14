<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = ['cpf', 'nome_completo', 'contato', 'rua', 'numero', 'bairro', 'cidade', 'estado'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function hasVerifiedEmail(): bool
    {
        return false;
    }
}
