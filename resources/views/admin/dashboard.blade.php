@extends('layouts.admin')

@section('content')
<div class="flex justify-between gap-4">
    <h2 class="md:text-xl text-xm font-semibold text-blue-800 mb-6 text-center">Bienvenido(a), {{ Auth::user()->name }}</h2>
    <h2 class="md:text-xl text-xm font-semibold text-gray-800 mb-6 text-center">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</h2>
</div>

@endsection