<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id'); // Define a coluna como UUID
            $table->string('token', 64)->unique();
            $table->string('device')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            // Chave estrangeira para a tabela users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_sessions');
    }
};
