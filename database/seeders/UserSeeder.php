<?php

namespace Database\Seeders;

use App\Enums\AccountStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PlanEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),
            'name' => 'Victor',
            'surname' => 'Pereira',
            'phone_number' => '31999999999',
            'email' => 'teste@teste.com',
            'password' => bcrypt('12345678'),
            'email_verified_at' => now(),
            'is_verified' => true,
            'verification_code' => null,
            'plan' => PlanEnum::PRO,
            'account_status' => AccountStatusEnum::ACTIVE,
            'payment_status' => PaymentStatusEnum::PAID,
            'plan_start_date' => now(),
            'plan_end_date' => now()->addYear(),
            'remember_token' => Str::random(10),
            'role' => RoleEnum::ADMIN,
        ]);
    }
}
