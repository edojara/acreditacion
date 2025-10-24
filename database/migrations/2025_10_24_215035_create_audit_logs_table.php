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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // login, logout, create, update, delete
            $table->string('model_type')->nullable(); // User, Role, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // quien realizó la acción
            $table->string('user_email')->nullable();
            $table->json('old_values')->nullable(); // valores anteriores
            $table->json('new_values')->nullable(); // valores nuevos
            $table->string('ip_address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'action']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
