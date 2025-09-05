<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Lectura_Sensores;
use App\Models\Incubadora;

use Illuminate\Http\Request;

class AvicultorController extends Controller
{

public function graficos()
{
    $incubadoras = Incubadora::where('usuario_id', auth()->id())->get();
    return view('avicultor.graficos', compact('incubadoras'));
}

public function lecturasPorIncubadora($incubadoraId, Request $request)
{
    $userId = auth()->id();

    $procesoId = \App\Models\Incubacion::where('incubadora_id', $incubadoraId)
        ->whereHas('incubadora', function ($q) use ($userId) {
            $q->where('usuario_id', $userId);
        })
        ->value('id');

    if (!$procesoId) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $lecturas = Lectura_Sensores::where('proceso_id', $procesoId)->orderBy('fecha_hora');

    if ($request->has(['gestion', 'mes'])) {
        $lecturas->whereYear('fecha_hora', $request->gestion)
                 ->whereMonth('fecha_hora', $request->mes);
    }

    $manana = [];
    $noche = [];

    foreach ($lecturas->get() as $lectura) {
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

      public function index()
    {
        
        $rol = User::all(); // o ->paginate(10) si deseas paginación
        return view('avicultor.inicioavicultor', compact('rol'));
    }

    public function inicio() {
        return view('avicultor.inicioavicultor');
    }


    public function historial() {
        return view('avicultor.reportes');
    }

    public function ciclos() {
        return view('avicultor.crearproceso');
    }

    public function nosotros() {
        return view('avicultor.nosotros');
    }



}