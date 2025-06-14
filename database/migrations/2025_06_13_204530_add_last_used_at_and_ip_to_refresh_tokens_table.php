<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('refresh_tokens', function (Blueprint $table) {
            $table->timestamp('last_used_at')->nullable();
            $table->string('ip_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('refresh_tokens', function (Blueprint $table) {
            //
        });
    }
};
