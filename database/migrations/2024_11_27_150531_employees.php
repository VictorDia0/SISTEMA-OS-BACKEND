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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name', 20)->nullable();
            $table->string('surname', 100)->nullable();
            $table->string('RG', 15)->unique()->nullable();
            $table->string('CPF', 11)->unique()->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password');
            $table->string('CEP', 8);
            $table->string('road', 100);
            $table->string('number', 10);
            $table->string('neighborhood', 50);
            $table->char('state', 2);
            $table->enum('role', ['admin', 'manager', 'staff']); // Níveis de acesso
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
