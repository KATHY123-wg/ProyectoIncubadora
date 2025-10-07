<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Incubacion;
use App\Models\Lectura_sensores;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AlarmaService
{
    // Umbrales por defecto
    const TEMP_MIN = 36.5;
    const TEMP_MAX = 38.5;
    const HUM_MIN  = 45.0;
    const HUM_MAX  = 60.0;

    // Ventana para "SIN_LECTURAS" (minutos sin recibir datos)
    const MIN_SIN_LECTURAS = 3;

    /**
     * Llamar inmediatamente después de guardar una lectura.
     */
    public static function evaluarDespuesDeGuardarLectura(Lectura_sensores $lectura): void
    {
        // Hallar incubadora_id a partir del proceso (tu lectura no lo trae directo)
        $proceso = Incubacion::select('id','incubadora_id')->find($lectura->proceso_id);
        if (!$proceso) return;
        $incuId = $proceso->incubadora_id;
        $procId = $proceso->id;

        // 1) SENSOR
        if ((int)$lectura->error_sensor === 1) {
            self::openOrBump($incuId,$procId,'SENSOR','DHT22_SIN_DATOS','critical',
                'El sensor DHT22 reportó error.');
        } else {
            self::closeIfOpen($incuId,'DHT22_SIN_DATOS');
        }

        // 2) MOTOR
        if ((int)$lectura->error_motor === 1) {
            self::openOrBump($incuId,$procId,'MOTOR','MOTOR_ATASCADO','critical',
                'Error de motor (atasco/sobrecorriente).');
        } else {
            self::closeIfOpen($incuId,'MOTOR_ATASCADO');
        }

        // 3) LÁMPARA / FOCO
        if ((int)$lectura->error_foco === 1) {
            self::openOrBump($incuId,$procId,'LAMPARA','LAMPARA_SIN_CALENTAR','warning',
                'Se detectó error en la lámpara/calefacción.');
        } else {
            self::closeIfOpen($incuId,'LAMPARA_SIN_CALENTAR');
        }

        // 4) TEMPERATURA fuera de rango
        if (!is_null($lectura->temperatura) && ($lectura->temperatura < self::TEMP_MIN || $lectura->temperatura > self::TEMP_MAX)) {
            self::openOrBump(
                $incuId,$procId,'TEMPERATURA','TEMP_FUERA_RANGO','warning',
                'Temperatura fuera de rango.', $lectura->temperatura,
                $lectura->temperatura < self::TEMP_MIN ? self::TEMP_MIN : self::TEMP_MAX
            );
        } else {
            self::closeIfOpen($incuId,'TEMP_FUERA_RANGO');
        }

        // 5) HUMEDAD fuera de rango
        if (!is_null($lectura->humedad) && ($lectura->humedad < self::HUM_MIN || $lectura->humedad > self::HUM_MAX)) {
            self::openOrBump(
                $incuId,$procId,'HUMEDAD','HUM_FUERA_RANGO','warning',
                'Humedad fuera de rango.', $lectura->humedad,
                $lectura->humedad < self::HUM_MIN ? self::HUM_MIN : self::HUM_MAX
            );
        } else {
            self::closeIfOpen($incuId,'HUM_FUERA_RANGO');
        }
    }

    /**
     * Programable por scheduler: alerta por falta de lecturas recientes.
     */
    public static function revisarComunicacion(): void
    {
        $corte = now()->subMinutes(self::MIN_SIN_LECTURAS);

        // Última lectura por incubadora (vía join procesos)
        $rows = DB::table('incubadoras as i')
            ->leftJoin('procesos as p', 'p.incubadora_id','=','i.id')
            ->leftJoin('lectura_sensores as ls','ls.proceso_id','=','p.id')
            ->select('i.id as incubadora_id', DB::raw('MAX(ls.fecha_hora) as last_fh'))
            ->groupBy('i.id')
            ->get();

        foreach ($rows as $r) {
            $last = $r->last_fh ? Carbon::parse($r->last_fh) : null;
            if (!$last || $last->lt($corte)) {
                self::openOrBump($r->incubadora_id, null, 'COMUNICACION', 'SIN_LECTURAS', 'critical',
                    'No se reciben lecturas recientes de la incubadora.');
            } else {
                self::closeIfOpen($r->incubadora_id,'SIN_LECTURAS');
            }
        }
    }

    // ===== Helpers =====

    public static function openOrBump($incubadoraId,$procesoId,$tipo,$codigo,$nivel,$mensaje,$valor=null,$umbral=null): void
    {
        $a = Alerta::where('incubadora_id',$incubadoraId)
            ->where('codigo',$codigo)
            ->where('estado','abierta')
            ->first();

        if ($a) {
            $a->increment('ocurrencias');
            $a->update([
                'mensaje' => $mensaje,
                'valor_actual' => $valor,
                'umbral' => $umbral,
                // si estaba silenciada y ya pasó el tiempo, dejarla activa
                'silenciada_hasta' => ($a->silenciada_hasta && $a->silenciada_hasta->isFuture()) ? $a->silenciada_hasta : null,
                'ultima_actualizacion' => now(),
            ]);
        } else {
            Alerta::create([
                'incubadora_id' => $incubadoraId,
                'proceso_id'    => $procesoId,
                'tipo'          => $tipo,
                'codigo'        => $codigo,
                'nivel'         => $nivel,
                'mensaje'       => $mensaje,
                'valor_actual'  => $valor,
                'umbral'        => $umbral,
                'estado'        => 'abierta',
            ]);
        }
    }

    public static function closeIfOpen($incubadoraId,$codigo): void
    {
        $a = Alerta::where('incubadora_id',$incubadoraId)
            ->where('codigo',$codigo)
            ->where('estado','abierta')
            ->first();

        if ($a) {
            $a->update([
                'estado' => 'resuelta',
                'resuelta_en' => now(),
                'resuelta_por' => auth()->id(),
                'ultima_actualizacion' => now(),
            ]);
        }
    }
}
