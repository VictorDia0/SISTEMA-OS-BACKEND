<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Enums\RoleEnum;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['key' => RoleEnum::ADMIN->value, 'name' => 'Administrador'],
            ['key' => RoleEnum::USER->value, 'name' => 'Usuário Padrão'],
            ['key' => RoleEnum::MANAGER->value, 'name' => 'Gerente'],
            ['key' => RoleEnum::SUPPORT->value, 'name' => 'Suporte'],
            ['key' => RoleEnum::FINANCE->value, 'name' => 'Financeiro'],
            ['key' => RoleEnum::TECHNICIAN->value, 'name' => 'Técnico'],
            ['key' => RoleEnum::CLIENT->value, 'name' => 'Cliente'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['key' => $role['key']],
                ['name' => $role['name']]
            );
        }
    }
}
