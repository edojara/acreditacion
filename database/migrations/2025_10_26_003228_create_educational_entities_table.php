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
        Schema::create('educational_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique(); // Código único de la entidad
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->default('Chile');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('type', ['universidad', 'instituto', 'colegio', 'centro_educativo', 'otro'])->default('universidad');
            $table->enum('status', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->json('metadata')->nullable(); // Para campos adicionales flexibles
            $table->timestamps();

            // Índices
            $table->index(['type', 'status']);
            $table->index(['city', 'region']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_entities');
    }
};
