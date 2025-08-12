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
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id')->index('model_has_permissions_permission_id_foreign');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table
                ->unsignedBigInteger('evento_id')
                ->nullable()
                ->index('model_has_permissions_evento_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_permissions');
    }
};
