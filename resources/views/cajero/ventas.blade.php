@extends('cajero.iniciocajero')  

@section('titulo', 'Ventas')
@section('titulo_nav', 'Vendedor')



@section('contenido')
    @livewire('vendedor.venta-form')
@endsection
