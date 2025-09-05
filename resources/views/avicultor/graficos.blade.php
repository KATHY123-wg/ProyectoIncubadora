@extends('avicultor.inicioavicultor')
@section('titulo', 'Gráficos Avicultor')
@section('contenido')
@livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('scripts')

    <h2 class="mb-4 fw-bold">Gráficos de Temperatura</h2>
    @livewire('avicultor.avicultor-graficos')
@endsection
