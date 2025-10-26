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
        Schema::table('educational_entities', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropIndex(['type', 'status']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('educational_entities', function (Blueprint $table) {
            $table->enum('status', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->dropIndex(['type']);
            $table->index(['type', 'status']);
        });
    }
};
