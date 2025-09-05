<?php

namespace App\Http\Controllers;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
   
use App\Models\Detalle_venta;

use Carbon\Carbon;


use Illuminate\Http\Request;


class VentaController extends Controller

{
     public function index() {
        return view('cajero.ventas');
    }
   

    public function nosotros() {
        return view('cajero.nosotros');
    }
    

public function inicio()
{
    $tz = 'America/La_Paz';
    $hoy = Carbon::now($tz)->toDateString();
    $inicioMes = Carbon::now($tz)->startOfMonth()->toDateString();
    $finMes    = Carbon::now($tz)->endOfMonth()->toDateString();

    // KPIs hoy
    $qHoy = Venta::whereDate('fecha_venta', $hoy)
                 ->where('usuario_id', Auth::id())
                 ->where('estado', 1);

    $ventasHoy = (clone $qHoy)->count();
    $montoHoy  = (clone $qHoy)->sum('total_bs');

    // KPIs mes
    $qMes = Venta::whereBetween('fecha_venta', [$inicioMes.' 00:00:00', $finMes.' 23:59:59'])
                 ->where('usuario_id', Auth::id())
                 ->where('estado', 1);

    $ventasMes = (clone $qMes)->count();
    $montoMes  = (clone $qMes)->sum('total_bs');

    // Top incubadoras del mes
    $topIncubadoras = Detalle_venta::join('ventas','detalle_ventas.venta_id','=','ventas.id')
        ->join('incubadoras','detalle_ventas.incubadora_id','=','incubadoras.id')
        ->whereBetween('ventas.fecha_venta', [$inicioMes.' 00:00:00', $finMes.' 23:59:59'])
        ->where('ventas.usuario_id', Auth::id())
        ->where('ventas.estado', 1)
        ->groupBy('incubadoras.codigo')
        ->selectRaw('incubadoras.codigo as codigo, COUNT(*) as cantidad')
        ->orderByDesc('cantidad')
        ->limit(5)
        ->get()
        ->map(fn($r) => ['codigo'=>$r->codigo, 'cantidad'=>(int)$r->cantidad])
        ->toArray();

    // Ventas recientes
    $ventasRecientes = Venta::where('usuario_id', Auth::id())
        ->orderByDesc('fecha_venta')
        ->limit(10)
        ->get(['id','fecha_venta','total_bs','estado'])
        ->toArray();

    return view('cajero.iniciocajero', compact(
        'ventasHoy','montoHoy','ventasMes','montoMes','topIncubadoras','ventasRecientes'
    ));
}

}
