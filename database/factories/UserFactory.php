<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\PlanEnum;
use App\Enums\AccountStatusEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'plan' => $this->faker->randomElement(PlanEnum::cases()),
            'account_status' => $this->faker->randomElement(AccountStatusEnum::cases()),
            'payment_status' => $this->faker->randomElement(PaymentStatusEnum::cases()),
            'email_verified_at' => null,
            'verification_code' => null,
            'is_verified' => false,
            'is_active' => true,
            'plan_start_date' => now(),
            'plan_end_date' => now()->addMonth(),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
            'is_verified' => true,
        ]);
    }

    public function withVerificationCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_code' => str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT),
        ]);
    }
}
