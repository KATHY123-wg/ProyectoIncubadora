@extends('admin.inicioadmin')

@section('titulo', 'Gestión de Incubadoras')
@section('titulo_nav', 'Administrador')

{{-- Usa el mismo sidebar que en inicioadmin (puedes copiarlo o extraerlo a un partial) --}}


@section('contenido')
    <livewire:admin.incubadoras-admin />
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('reportes.incubadoras.pdf', request()->query()) }}" class="btn btn-danger btn-sm">PDF</a>
        <a href="{{ route('reportes.incubadoras.csv', request()->query()) }}" class="btn btn-success btn-sm">CSV</a>
        <a href="{{ route('reportes.incubadoras.xls', request()->query()) }}" class="btn btn-warning btn-sm text-white">XLS</a>
    </div>

@endsection
