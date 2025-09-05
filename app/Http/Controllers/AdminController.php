<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Lectura_Sensores;
use App\Models\Incubadora;
use App\Models\Incubacion;



class AdminController extends Controller

{
    public function lecturasPorIncubadora($procesoId)
{
    $lecturas = \App\Models\Lectura_Sensores::where('proceso_id', $procesoId)
        ->orderBy('fecha_hora')
        ->get();

    $manana = [];
    $noche = [];

    foreach ($lecturas as $lectura) {
        $hora = date('H:i', strtotime($lectura->fecha_hora));
        $punto = [
            'x' => date('d/m H:i', strtotime($lectura->fecha_hora)),
            'yTemp' => (float) $lectura->temperatura,
            'yHum' => (float) $lectura->humedad
        ];

        if ($hora >= '06:00' && $hora < '12:00') {
            $manana[] = $punto;
        } elseif ($hora >= '18:00' && $hora <= '23:59') {
            $noche[] = $punto;
        }
    }

    return response()->json([
        'manana' => $manana,
        'noche' => $noche
    ]);
}




    

     public function inicio() {
        return view('admin.inicioadmin');
    }

    

    

    public function ciclos() {
        return view('admin.ciclos');
    }

    public function historial() {
        return view('admin.reportes');
    }

    public function ventas() {
        return view('admin.ventas');
    }

    public function usuarios() {
        return view('admin.usuarios');
    }
public function obtenerErrores($incubadoraId)
{
    $errores = \App\Models\Lectura_Sensores::whereHas('proceso.incubadora', function ($query) use ($incubadoraId) {
        $query->where('id', $incubadoraId);
    })
    ->where(function ($q) {
        $q->where('error_motor', true)
          ->orWhere('error_foco', true)
          ->orWhere('error_sensor', true);
    })
    ->orderByDesc('fecha_hora')
    ->get();

    $resultado = [];

    foreach ($errores as $e) {
        if ($e->error_motor) {
            $resultado[] = [
                'fecha' => $e->fecha_hora,
                'tipo' => 'Motor',
                'descripcion' => 'Falla en el motor detectada'
            ];
        }
        if ($e->error_foco) {
            $resultado[] = [
                'fecha' => $e->fecha_hora,
                'tipo' => 'Foco',
                'descripcion' => 'Falla en el foco o sistema de calefacción'
            ];
        }
        if ($e->error_sensor) {
            $resultado[] = [
                'fecha' => $e->fecha_hora,
                'tipo' => 'Sensor',
                'descripcion' => 'Falla en el sensor de temperatura/humedad'
            ];
        }
    }

    return response()->json($resultado);
}



}


