<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Incubacion;                 // local
use App\Models\Remote\RemoteLectura;    // remoto

class AvicultorMetricsController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->rol, ['admin','avicultor'])) {
            return response()->json(['ok'=>false,'msg'=>'No autorizado'], 403);
        }

        // 1) proceso activo del usuario (LOCAL)
        $proceso = Incubacion::query()
            ->select('procesos.*')
            ->join('incubadoras','incubadoras.id','=','procesos.incubadora_id')
            ->when($user->rol === 'avicultor', fn($q) => $q->where('incubadoras.usuario_id',$user->id))
            ->where('procesos.estado', 1)
            ->orderByDesc('procesos.fecha_inicio')
            ->first();

        if (!$proceso) {
            return response()->json([
                'ok'=>true, 'temp'=>null, 'hum'=>null,
                'diasTranscurridos'=>null, 'diasRestantes'=>null,
                'msg'=>'Sin proceso activo'
            ]);
        }

        // 2) última lectura del proceso (REMOTO)
        $lectura = RemoteLectura::where('proceso_id', $proceso->id)
                    ->orderByDesc('fecha_hora')->first();

        // 3) días (LOCAL)
        $inicio   = Carbon::parse($proceso->fecha_inicio);
        $estimada = Carbon::parse($proceso->fecha_estimada);
        $hoy      = now();
        $diasTranscurridos = $inicio->diffInDays($hoy);
        $diasRestantes     = max(0, $hoy->diffInDays($estimada, false));

        return response()->json([
            'ok' => true,
            'temp' => $lectura?->temperatura,
            'hum'  => $lectura?->humedad,
            'diasTranscurridos' => $diasTranscurridos,
            'diasRestantes'     => $diasRestantes,
            'at' => $lectura?->fecha_hora,
        ])->header('Cache-Control','no-store, no-cache, must-revalidate, max-age=0');
    }
}
