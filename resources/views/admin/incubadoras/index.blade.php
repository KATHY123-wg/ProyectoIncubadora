@extends('admin.inicioadmin')

@section('titulo', 'Gestión de Incubadoras')
@section('titulo_nav', 'Administrador')

{{-- Usa el mismo sidebar que en inicioadmin (puedes copiarlo o extraerlo a un partial) --}}


@section('contenido')
    <livewire:admin.incubadoras-admin />
@endsection
