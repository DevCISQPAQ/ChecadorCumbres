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
            $table->unsignedBigInteger('empleado_id'); // FK a empleados.id
            $table->timestamp('hora_entrada')->nullable();
            $table->timestamp('hora_salida')->nullable();
            $table->boolean('retardo')->default(false);
            $table->timestamps();

            // Llave foránea y restricción para integridad referencial
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
