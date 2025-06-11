<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'cpf' => '12345678901',
                'nome_completo' => 'João da Silva',
                'contato' => '(31) 99999-1111',
                'rua' => 'Rua das Flores',
                'numero' => '123',
                'bairro' => 'Centro',
                'cidade' => 'Almenara',
                'estado' => 'MG',
            ],
            [
                'cpf' => '23456789012',
                'nome_completo' => 'Maria Oliveira',
                'contato' => '(31) 98888-2222',
                'rua' => 'Av. Brasil',
                'numero' => '456',
                'bairro' => 'Jardins',
                'cidade' => 'Teófilo Otoni',
                'estado' => 'MG',
            ],
            [
                'cpf' => '34567890123',
                'nome_completo' => 'Carlos Pereira',
                'contato' => '(31) 97777-3333',
                'rua' => 'Rua dos Lírios',
                'numero' => '789',
                'bairro' => 'Vila Nova',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
