<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {

            $table->id();

            $table->string('n_empleado')->unique();

            $table->string('nombres');

            $table->string('apellido_paterno');

            $table->string('apellido_materno');

            $table->foreignId('departamento_id')
                ->nullable()
                ->constrained('departamentos')
                ->nullOnDelete();

            $table->string('puesto');

            $table->string('email')
                ->nullable();

            $table->text('foto')
                ->nullable();

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            // índices
            $table->index('nombres');

            $table->index([
                'apellido_paterno',
                'apellido_materno'
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
