<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Chave primária como UUID
            $table->uuid('user_id'); // Relacionamento com a tabela users (UUID)

            $table->string('name', 50);
            $table->string('surname', 50);
            $table->string('RG', 20)->nullable();
            $table->string('CPF', 14)->unique();
            $table->string('telefone', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password');
            $table->string('CEP', 9)->nullable();
            $table->string('rua', 100)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 50)->nullable();
            $table->string('estado', 50)->nullable();
            $table->enum('situacao', ['ativo', 'inativo'])->default('ativo');
            $table->enum('permissao', ['admin', 'tecnico', 'secretario'])->default('tecnico');
            $table->timestamps();

            // Configurando chave estrangeira
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Exclusão em cascata
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
