<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('nombres');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('departamento');
            $table->string('puesto');
            $table->string('email')->nullable();
            $table->string('tipo_horario');
            $table->text('foto')->nullable();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
