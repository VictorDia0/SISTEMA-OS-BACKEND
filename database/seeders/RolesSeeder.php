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
             RoleEnum::ADMIN->value,
             RoleEnum::USER->value,
             RoleEnum::MANAGER->value,
             RoleEnum::SUPPORT->value,
             RoleEnum::FINANCE->value,
             RoleEnum::TECHNICIAN->value,
             RoleEnum::CLIENT->value,
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }
    }
}
