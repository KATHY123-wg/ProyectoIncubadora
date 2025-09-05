@extends('admin.inicioadmin')  

@section('titulo', 'Ventas')
@section('titulo_nav', 'Administrador')



@section('contenido')
    @livewire('admin.venta-form')
@endsection
