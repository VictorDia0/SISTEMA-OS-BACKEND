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
            $table->string('name', 20)->nullable();
            $table->string('surname', 100)->nullable();
            $table->string('RG', 15)->unique()->nullable();
            $table->string('CPF', 11)->unique()->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password');
            $table->string('CEP', 8);
            $table->string('rua', 100);
            $table->string('numero', 10);
            $table->string('bairro', 50);
            $table->char('estado', 2);
            $table->enum('situacao', ['Ativo', 'Inativo'])->default('Ativo');
            $table->enum('permissao', ['admin', 'tecnico', 'secretario'])->default('tecnico');
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
