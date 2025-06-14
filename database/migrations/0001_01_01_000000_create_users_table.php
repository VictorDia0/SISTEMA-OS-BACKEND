<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->nullable();
            $table->string('surname', 50)->nullable();
            $table->string('phone_number', 20)->nullable()->index();
            $table->string('email', 255)->unique();
            $table->string('password', 60);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code', 6)->nullable()->index();
            $table->string('role')->default('user');
            $table->string('is_verified')->default(false);
            $table->boolean('is_active')->default(true);

            $table->string('plan')->default('free');
            $table->string('account_status')->default('pending');
            $table->string('payment_status')->default('due');

            $table->timestamp('plan_start_date')->nullable();
            $table->timestamp('plan_end_date')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
