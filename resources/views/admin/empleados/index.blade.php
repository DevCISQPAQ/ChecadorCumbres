@extends('layouts.admin')

@section('content')
<h2 class="text-2xl font-semibold text-gray-800 mb-6">Lista de Empleados</h2>

<!-- Tarjetas de conteo de estudiantes por grado escolar -->


<div x-data="{ buscar: '{{ request('buscar', '') }}', eliminarActivo: false  }">
    <!-- Formulario de búsqueda -->


    

    <!-- Tabla de estudiantes -->
   
</div>
@endsection