<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Incubacion;
use App\Models\Incubadora;
use App\Models\Venta;
class ReporteController extends Controller
{
    public function index() {
        return view('admin.reportes.index');
    }

   
    public function procesos() {
        $usuarios = User::where('rol', 'avicultor')->get();
        return view('admin.reportes.procesos', compact('usuarios'));
    }

    public function getIncubadoras($usuario_id) {
        return Incubadora::where('usuario_id', $usuario_id)->select('id', 'codigo')->get();
    }

    public function getProceso($incubadora_id, $gestion) {
        $proceso = Incubacion::where('incubadora_id', $incubadora_id)
                        ->whereYear('fecha_inicio', $gestion)
                        ->first();

        if (!$proceso) return response()->json(null);

        return response()->json([
            'fecha_inicio' => $proceso->fecha_inicio,
            'fecha_fin' => $proceso->fecha_fin,
            'huevos_inicio' => $proceso->huevos_inicio,
            'huevos_eclosionados' => $proceso->huevos_eclosionados,
            'errores_motor' => $proceso->errores_motor,
            'errores_lampara' => $proceso->errores_lampara,
            'errores_sensor' => $proceso->errores_sensor,
        ]);
    }
    public function ventas(Request $request)
    {
        // Solo usuarios con rol 'vendedor'
        $usuarios = User::where('rol', 'vendedor')->get();

        $ventas = collect();
        $nombreCajero = null;

        if ($request->filled('usuario_id') && $request->filled('gestion')) {
            $query = Venta::with(['incubadora', 'avicultor', 'cajero'])
                ->where('usuario_id', $request->usuario_id)
                ->whereYear('fecha_venta', $request->gestion);

            if ($request->filled('mes')) {
                $query->whereMonth('fecha_venta', $request->mes);
            }

            $ventas = $query->get();

            $cajero = User::find($request->usuario_id);
            $nombreCajero = $cajero ? "{$cajero->nombre} {$cajero->apellido1}" : 'Desconocido';
        }

        return view('admin.reportes.ventas', compact('usuarios', 'ventas', 'nombreCajero'));
    }



}
