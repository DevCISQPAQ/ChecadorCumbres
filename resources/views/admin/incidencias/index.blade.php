@extends('layouts.admin')

@section('content')

@if ($errors->any())
<div x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition
    class="mb-5 bg-red-100 border border-red-400 text-red-700 p-4 rounded">
    <ul class="list-disc pl-5 text-sm">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    <!-- ===================================================== -->
    <!-- Permisos -->
    <!-- ===================================================== -->

    <div class="bg-white p-6 rounded-xl shadow">

        <div class="mb-3">
            <h1 class="text-2xl font-bold text-gray-800">
                Registrar permisos
            </h1>

            <p class="text-sm text-gray-500">
                Asigna un periodo de permisos a un empleado.
            </p>
        </div>


    </div>

    <!-- ===================================================== -->
    <!-- Subsidio de incapacidad -->
    <!-- ===================================================== -->

    <div class="bg-white p-6 rounded-xl shadow h-fit">
        <div class="mb-3">
            <h1 class="text-2xl font-bold text-gray-800">
                Registrar subsidio de incapacidad
            </h1>

            <p class="text-sm text-gray-500">
                Asigna un periodo de subsidio de incapacidad a un empleado.
            </p>
        </div>
    </div>

</div>


@endsection