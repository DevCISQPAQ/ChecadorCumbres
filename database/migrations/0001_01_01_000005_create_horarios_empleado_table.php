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
        Schema::create('horarios_empleado', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empleado_id')
                ->constrained()
                ->cascadeOnDelete();

            // 1=lunes 7=domingo
            $table->tinyInteger('dia_semana');

            $table->time('hora_entrada');

            $table->time('hora_salida');

            // comida
            $table->time('hora_salida_comida')
                ->nullable();

            $table->time('hora_regreso_comida')
                ->nullable();

            // tolerancia retardo
            $table->integer('tolerancia_minutos')
                ->default(10);

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'empleado_id',
                'dia_semana'
            ]);
            
            $table->index([
                'empleado_id',
                'dia_semana'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_empleado');
    }
};
