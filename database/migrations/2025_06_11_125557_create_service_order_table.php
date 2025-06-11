<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_order', function (Blueprint $table) {
            $table->id();
            $table->string('client');
            $table->string('responsavel');
            $table->string('status');
            $table->timestamp('data_inicial')->nullable();
            $table->timestamp('data_final')->nullable();
            $table->integer('garantia')->default(0);
            $table->string('descricao_servico')->nullable();
            $table->string('defeito')->nullable();
            $table->string('observacoes')->nullable();
            
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('service_order');
    }
};
