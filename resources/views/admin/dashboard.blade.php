@extends('layouts.admin')

@section('content')
<h2 class="text-2xl font-semibold text-gray-800 mb-6">Bienvenido(a), {{ Auth::user()->name }}</h2>

{{-- Tarjetas resumen --}}

{{-- Sección adicional --}}



@endsection