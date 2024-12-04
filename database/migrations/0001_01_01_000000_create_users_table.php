<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->nullable();
            $table->string('surname', 50)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code')->nullable();

            $table->enum('plan', ['basic', 'medium', 'pro']);
            $table->enum('account_status', ['active', 'pending', 'suspended']);
            $table->enum('payment_status', ['paid', 'due', 'overdue']);

            $table->timestamp('plan_start_date')->nullable(); // Data de início do plano
            $table->timestamp('plan_end_date')->nullable(); // Data de término do plano

            $table->rememberToken();
            $table->timestamps();

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
