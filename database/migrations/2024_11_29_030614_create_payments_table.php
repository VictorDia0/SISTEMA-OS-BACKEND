<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->enum('plan', ['basic', 'medium', 'pro']);
            $table->decimal('amount', 8, 2); // Valor pago
            $table->enum('payment_status', ['completed', 'pending', 'failed']);
            $table->timestamp('due_date')->nullable(); // Permitir null
            $table->timestamp('payment_date')->nullable(); // Permitir null

            $table->timestamps();

            // Chave estrangeira para users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
