<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PlanEnum;
use App\Enums\AccountStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->nullable();
            $table->string('surname', 70)->nullable();
            $table->string('phone_number', 20)->nullable()->index();
            $table->string('email', 255)->unique();
            $table->string('password', 60);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code', 6)->nullable()->index();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);

            $table->enum('role', array_column(RoleEnum::cases(), 'value'))->default(RoleEnum::USER->value);
            $table->enum('plan', array_column(PlanEnum::cases(), 'value'))->default(PlanEnum::FREE->value);
            $table->enum('account_status', array_column(AccountStatusEnum::cases(), 'value'))->default(AccountStatusEnum::PENDING->value);
            $table->enum('payment_status', array_column(PaymentStatusEnum::cases(), 'value'))->default(PaymentStatusEnum::DUE->value);

            $table->timestamp('plan_start_date')->nullable();
            $table->timestamp('plan_end_date')->nullable();

            $table->boolean('terms')->default(false);

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
