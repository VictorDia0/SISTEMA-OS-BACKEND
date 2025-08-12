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
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->index('model_has_roles_role_id_foreign');
            $table->string('model_type')->nullable();
            $table->uuid('model_id');
            $table
                ->unsignedBigInteger('evento_id')
                ->nullable()
                ->index('model_has_roles_evento_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_roles');
    }
};
