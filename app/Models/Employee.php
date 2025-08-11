<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Employee extends Model
{
    public $incrementing = false; // Desativa auto-incremento

    protected $keyType = 'string'; // Define o tipo da chave primária como string

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString(); // Gera um UUID
            }
        });
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'RG',
        'CPF',
        'telefone',
        'celular',
        'email',
        'password',
        'CEP',
        'rua',
        'numero',
        'bairro',
        'estado',
        'situacao',
        'permissao',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Verifica se o usuário é administrador.
     */
    public function isAdmin()
    {
        return $this->Permissao === 'admin';
    }

    /**
     * Verifica se o usuário é técnico.
     */
    public function isTecnico()
    {
        return $this->Permissao === 'tecnico';
    }

    /**
     * Verifica se o usuário é secretário.
     */
    public function isSecretario()
    {
        return $this->Permissao === 'secretario';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
