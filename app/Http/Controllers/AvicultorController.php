<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Lectura_Sensores as LecturaSensores;
use App\Models\Incubadora;
use App\Models\Incubacion;
use Carbon\Carbon;

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

    $lecturas = LecturaSensores::where('proceso_id', $procesoId)->orderBy('fecha_hora');

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

    public function inicio()
    {
        $userId = auth()->id();

        // 1) Incubadoras del usuario
        $incubadorasIds = Incubadora::where('usuario_id', $userId)->pluck('id');

        // 2) Proceso ACTIVO más reciente de cualquiera de sus incubadoras
        $procesoActual = Incubacion::whereIn('incubadora_id', $incubadorasIds)
            ->where('estado', 'ACTIVO')
            ->orderByDesc('fecha_inicio')
            ->first();

        $temp = null;
        $hum  = null;
        $diasTranscurridos = null;
        $diasRestantes = null;

        if ($procesoActual) {
            // 3) Última lectura
            $ultima = LecturaSensores::where('proceso_id', $procesoActual->id)
                ->orderByDesc('fecha_hora')
                ->first();

            if ($ultima) {
                $temp = (float) $ultima->temperatura;
                $hum  = (float) $ultima->humedad;
            }

            // 4) Cálculo de días transcurridos/restantes
            $now    = Carbon::now();
            $inicio = $procesoActual->fecha_inicio ? Carbon::parse($procesoActual->fecha_inicio) : null;

            if ($inicio) {
                $diasTranscurridos = $inicio->diffInDays($now);
            }

            // fecha_fin_estimada o duracion_dias
            if (!empty($procesoActual->fecha_fin_estimada)) {
                $fin = Carbon::parse($procesoActual->fecha_fin_estimada);
            } elseif ($inicio && !empty($procesoActual->duracion_dias)) {
                $fin = $inicio->copy()->addDays((int) $procesoActual->duracion_dias);
            } else {
                $fin = null;
            }

            if ($fin) {
                $dif = $now->diffInDays($fin, false);
                $diasRestantes = $dif > 0 ? $dif : 0;
            }
        }

        // Pasa todo a la vista que ya tienes
        return view('avicultor.inicioavicultor', compact(
            'procesoActual', 'temp', 'hum', 'diasTranscurridos', 'diasRestantes'
        ));
    }

    // === Endpoint JSON para refrescar tarjetas sin recargar la página ===
    public function metrics()
    {
        $userId = auth()->id();
        $incubadorasIds = \App\Models\Incubadora::where('usuario_id', $userId)->pluck('id');

        $procesoActual = Incubacion::whereIn('incubadora_id', $incubadorasIds)
            ->where('estado', 'ACTIVO')
            ->orderByDesc('fecha_inicio')
            ->first();

        if (!$procesoActual) {
            return response()->json(['ok' => false, 'msg' => 'Sin proceso activo'], 200);
        }

        $ultima = LecturaSensores::where('proceso_id', $procesoActual->id)
            ->orderByDesc('fecha_hora')
            ->first();

        $temp = $ultima ? (float)$ultima->temperatura : null;
        $hum  = $ultima ? (float)$ultima->humedad : null;

        $now    = Carbon::now();
        $inicio = $procesoActual->fecha_inicio ? Carbon::parse($procesoActual->fecha_inicio) : null;

        $diasTranscurridos = $inicio ? $inicio->diffInDays($now) : null;

        if (!empty($procesoActual->fecha_fin_estimada)) {
            $fin = Carbon::parse($procesoActual->fecha_fin_estimada);
        } elseif ($inicio && !empty($procesoActual->duracion_dias)) {
            $fin = $inicio->copy()->addDays((int) $procesoActual->duracion_dias);
        } else {
            $fin = null;
        }
        $diasRestantes = null;
        if ($fin) {
            $dif = $now->diffInDays($fin, false);
            $diasRestantes = $dif > 0 ? $dif : 0;
        }

        return response()->json([
            'ok' => true,
            'temp' => $temp,
            'hum' => $hum,
            'diasTranscurridos' => $diasTranscurridos,
            'diasRestantes' => $diasRestantes,
            'proceso_id' => $procesoActual->id,
            'incubadora_id' => $procesoActual->incubadora_id,
            'ultima_fecha' => $ultima ? (string)$ultima->fecha_hora : null,
        ]);
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