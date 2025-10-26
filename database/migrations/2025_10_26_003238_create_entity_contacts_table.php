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
        Schema::create('entity_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_entity_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('position')->nullable(); // Cargo o rol en la entidad
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->enum('type', ['principal', 'academico', 'administrativo', 'tecnico', 'otro'])->default('principal');
            $table->boolean('is_primary')->default(false); // Contacto principal
            $table->text('notes')->nullable();
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->json('metadata')->nullable(); // Para campos adicionales
            $table->timestamps();

            // Índices
            $table->index(['educational_entity_id', 'type']);
            $table->index(['is_primary', 'status']);
            $table->unique(['educational_entity_id', 'email']); // Un email único por entidad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_contacts');
    }
};
