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
        Schema::create('checadas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empleado_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamp('fecha_hora');

            $table->enum('tipo', [
                'entrada',
                'salida_comida',
                'regreso_comida',
                'salida'
            ]);

            $table->timestamps();

            // índices IMPORTANTES
            $table->index([
                'empleado_id',
                'fecha_hora'
            ]);

            $table->index('fecha_hora');

            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checadas');
    }
};
