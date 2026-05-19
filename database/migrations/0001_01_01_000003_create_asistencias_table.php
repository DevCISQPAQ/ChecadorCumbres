<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            
            $table->id();

            $table->foreignId('empleado_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('fecha');

            $table->enum('estado', [
                'presente',
                'retardo',
                'falta',
                'permiso',
                'vacaciones',
                'festivo',
                'libre'
            ])->default('presente');

            $table->decimal('horas_trabajadas', 5, 2)
                ->nullable();

            $table->integer('minutos_retardo')
                ->default(0);

            $table->text('observaciones')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'empleado_id',
                'fecha'
            ]);

            $table->index('fecha');

            $table->index('estado');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
