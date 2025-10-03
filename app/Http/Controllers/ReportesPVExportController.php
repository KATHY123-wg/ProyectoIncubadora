<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Incubadora;
use App\Models\Incubacion;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;     // DomPDF para PDF
use Illuminate\Support\Carbon;  
use Illuminate\Support\Facades\Auth;
use App\Models\Lectura_sensores;
     // totales por mes en ventas

class ReportesPVExportController extends Controller
{
    /* ================== PROCESOS ================== */

    // PDF (usa una vista Blade en resources/views/exports)
    public function procesosPDF(Request $r)
    {
        $usuarioId     = (int) $r->query('usuarioId');
        $incubadora_id = (int) $r->query('incubadora_id');
        $gestion       = (int) $r->query('gestion');

        $proceso = Incubacion::with(['incubadora.usuario'])
            ->when($incubadora_id, fn($q) => $q->where('incubadora_id', $incubadora_id))
            ->when($gestion, fn($q) => $q->whereYear('fecha_inicio', $gestion))
            ->first();

        $incubadora = $incubadora_id ? Incubadora::find($incubadora_id) : null;
        $usuario    = $usuarioId ? User::find($usuarioId) : null;

        $pdf = Pdf::loadView('exports.procesos', [
            'proceso'     => $proceso,
            'incubadora'  => $incubadora,
            'usuario'     => $usuario,
            'gestion'     => $gestion,
            'generadoPor' => auth()->user()->usuario ?? auth()->id(),
            'fecha'       => now()->format('d/m/Y H:i'),
        ])->setPaper('A4','portrait');

        return $pdf->download('reporte_procesos_'.$gestion.'_'.now()->format('Ymd_His').'.pdf');
    }

    // XLS (tabla HTML con headers para Excel)
    public function procesosXLS(Request $r)
    {
        $usuarioId     = (int) $r->query('usuarioId');
        $incubadora_id = (int) $r->query('incubadora_id');
        $gestion       = (int) $r->query('gestion');

        $proceso = Incubacion::with(['incubadora.usuario'])
            ->when($incubadora_id, fn($q) => $q->where('incubadora_id', $incubadora_id))
            ->when($gestion, fn($q) => $q->whereYear('fecha_inicio', $gestion))
            ->first();

        $headers = [
            "Content-type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=reporte_procesos_{$gestion}_".now()->format('Ymd_His').".xls",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($proceso) {
            echo "<table border='1'>
                    <thead>
                      <tr>
                        <th>Incubadora</th>
                        <th>Fecha inicio</th>
                        <th>Huevos inicio</th>
                        <th>Huevos eclosionados</th>
                        <th>Errores motor</th>
                        <th>Errores lámpara</th>
                        <th>Errores sensor</th>
                      </tr>
                    </thead>
                    <tbody>";
            if ($proceso) {
                $fecha = \Illuminate\Support\Carbon::parse($proceso->fecha_inicio)->format('d/m/Y');
                echo "<tr>
                        <td>".($proceso->incubadora->codigo ?? '')."</td>
                        <td>{$fecha}</td>
                        <td>{$proceso->huevos_inicio}</td>
                        <td>{$proceso->huevos_eclosionados}</td>
                        <td>{$proceso->errores_motor}</td>
                        <td>{$proceso->errores_lampara}</td>
                        <td>{$proceso->errores_sensor}</td>
                      </tr>";
            } else {
                echo "<tr><td colspan='7'>Sin datos con los filtros seleccionados.</td></tr>";
            }
            echo    "</tbody>
                  </table>";
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ================== VENTAS ================== */

    public function ventasPDF(Request $r)
    {
        $usuario_id = (int) $r->query('usuario_id'); // vendedor
        $gestion    = (int) $r->query('gestion');

        $ventas = Venta::with(['detalleVentas.incubadora','avicultor','cajero'])
            ->when($usuario_id, fn($q)=>$q->where('usuario_id', $usuario_id))
            ->when($gestion, fn($q)=>$q->whereYear('fecha_venta', $gestion))
            ->orderBy('fecha_venta','asc')
            ->get();

        $porMes = $ventas->groupBy(fn($v)=>Carbon::parse($v->fecha_venta)->format('m'))
                         ->map(fn($g)=>$g->sum('total_bs'));

        $usuario = $usuario_id ? User::find($usuario_id) : null;

        $pdf = Pdf::loadView('exports.ventas', [
            'ventas'      => $ventas,
            'porMes'      => $porMes,
            'usuario'     => $usuario,
            'gestion'     => $gestion,
            'generadoPor' => auth()->user()->usuario ?? auth()->id(),
            'fecha'       => now()->format('d/m/Y H:i'),
        ])->setPaper('A4','portrait');

        return $pdf->download('reporte_ventas_'.$gestion.'_'.now()->format('Ymd_His').'.pdf');
    }

    public function ventasXLS(Request $r)
    {
        $usuario_id = (int) $r->query('usuario_id');
        $gestion    = (int) $r->query('gestion');

        $ventas = Venta::with(['detalleVentas.incubadora','avicultor','cajero'])
            ->when($usuario_id, fn($q)=>$q->where('usuario_id', $usuario_id))
            ->when($gestion, fn($q)=>$q->whereYear('fecha_venta', $gestion))
            ->orderBy('fecha_venta','asc')
            ->get();

        $porMes = $ventas->groupBy(fn($v)=>Carbon::parse($v->fecha_venta)->format('m'))
                         ->map(fn($g)=>$g->sum('total_bs'));

        $headers = [
            "Content-type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=reporte_ventas_{$gestion}_".now()->format('Ymd_His').".xls",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($porMes, $ventas) {
            // Resumen por mes
            echo "<table border='1'>
                    <thead><tr><th>Mes</th><th>Total (Bs.)</th></tr></thead>
                    <tbody>";
            $sumAnual = 0;
            $meses = [1=>'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
            for ($i=1; $i<=12; $i++) {
                $key = str_pad($i,2,'0',STR_PAD_LEFT);
                $monto = (float)($porMes[$key] ?? 0);
                $sumAnual += $monto;
                echo "<tr><td>{$meses[$i]}</td><td>".number_format($monto,2,',','.')."</td></tr>";
            }
            echo "<tr><th>Total Anual</th><th>".number_format($sumAnual,2,',','.')."</th></tr>";
            echo "</tbody></table>";

            // Detalle de ventas
            echo "<table border='1' style='margin-top:10px'>
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th># Ítems</th>
                        <th>Total (Bs.)</th>
                      </tr>
                    </thead>
                    <tbody>";
            if ($ventas->count()) {
                foreach ($ventas as $v) {
                    $fecha = Carbon::parse($v->fecha_venta)->format('d/m/Y');
                    $cliente = trim(($v->avicultor->nombre ?? '').' '.($v->avicultor->apellido1 ?? ''));
                    $items = $v->detalleVentas?->count() ?? 0;
                    $total = number_format($v->total_bs,2,',','.');
                    echo "<tr>
                            <td>{$fecha}</td>
                            <td>{$cliente}</td>
                            <td>{$items}</td>
                            <td>{$total}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Sin ventas en la gestión seleccionada.</td></tr>";
            }
            echo "</tbody></table>";
        };

        return response()->stream($callback, 200, $headers);
    }
    public function avicultorProcesosPDF(Request $r)
    {
        $userId        = Auth::id();
        $incubadora_id = (int) $r->query('incubadora_id');
        $gestion       = (int) $r->query('gestion');
        $mes           = $r->query('mes'); // opcional

        // Validar que la incubadora pertenezca al avicultor logueado
        $inc = Incubadora::where('id', $incubadora_id)->where('usuario_id', $userId)->firstOrFail();

        $q = Incubacion::with('incubadora:id,codigo')
            ->where('incubadora_id', $incubadora_id)
            ->whereYear('fecha_inicio', $gestion);

        if (!empty($mes)) {
            $q->whereMonth('fecha_inicio', $mes);
        }

        $items = $q->orderBy('fecha_inicio','desc')->get();

        // Mapear igual que tu componente Livewire
        $rows = $items->map(function ($proceso) {
            $ini   = $proceso->fecha_inicio ? Carbon::parse($proceso->fecha_inicio) : null;
            $finRx = $proceso->fecha_fin ?? $proceso->fecha_fin_estimada ?? null;
            $fin   = $finRx ? Carbon::parse($finRx) : null;

            $err = Lectura_sensores::selectRaw("
                    SUM(CASE WHEN error_motor=1  THEN 1 ELSE 0 END) AS motor,
                    SUM(CASE WHEN error_foco =1  THEN 1 ELSE 0 END) AS lampara,
                    SUM(CASE WHEN error_sensor=1 THEN 1 ELSE 0 END) AS sensor
                ")->where('proceso_id', $proceso->id)->first();

            return [
                'codigo_incubadora'   => $proceso->incubadora->codigo ?? '-',
                'nombre'              => $proceso->nombre,
                'fecha_inicio'        => $ini ? $ini->format('d/m/Y H:i') : '—',
                'fecha_fin'           => $fin ? $fin->format('d/m/Y H:i') : '—',
                'huevos_eclosionados' => (int)($proceso->cantidad_eclosionados ?? 0),
                'errores_motor'       => (int)($err?->motor ?? 0),
                'errores_lampara'     => (int)($err?->lampara ?? 0),
                'errores_sensor'      => (int)($err?->sensor ?? 0),
            ];
        })->toArray();

        $pdf = Pdf::loadView('exports/avicultor-procesos', [
            'rows'        => $rows,
            'incubadora'  => $inc,
            'gestion'     => $gestion,
            'mes'         => $mes,
            'generadoPor' => Auth::user()->usuario ?? Auth::id(),
            'fecha'       => now()->format('d/m/Y H:i'),
        ])->setPaper('A4', 'portrait');

        return $pdf->download('avicultor_procesos_'.$inc->codigo.'_'.$gestion.'_'.now()->format('Ymd_His').'.pdf');
    }

    // XLS (tabla HTML + headers)
    public function avicultorProcesosXLS(Request $r)
    {
        $userId        = Auth::id();
        $incubadora_id = (int) $r->query('incubadora_id');
        $gestion       = (int) $r->query('gestion');
        $mes           = $r->query('mes'); // opcional

        $inc = Incubadora::where('id', $incubadora_id)->where('usuario_id', $userId)->firstOrFail();

        $q = Incubacion::with('incubadora:id,codigo')
            ->where('incubadora_id', $incubadora_id)
            ->whereYear('fecha_inicio', $gestion);

        if (!empty($mes)) {
            $q->whereMonth('fecha_inicio', $mes);
        }

        $items = $q->orderBy('fecha_inicio','desc')->get();

        $rows = $items->map(function ($proceso) {
            $ini   = $proceso->fecha_inicio ? Carbon::parse($proceso->fecha_inicio) : null;
            $finRx = $proceso->fecha_fin ?? $proceso->fecha_fin_estimada ?? null;
            $fin   = $finRx ? Carbon::parse($finRx) : null;

            $err = Lectura_sensores::selectRaw("
                    SUM(CASE WHEN error_motor=1  THEN 1 ELSE 0 END) AS motor,
                    SUM(CASE WHEN error_foco =1  THEN 1 ELSE 0 END) AS lampara,
                    SUM(CASE WHEN error_sensor=1 THEN 1 ELSE 0 END) AS sensor
                ")->where('proceso_id', $proceso->id)->first();

            return [
                'Incubadora'          => $proceso->incubadora->codigo ?? '-',
                'Nombre del Proceso'  => $proceso->nombre,
                'Fecha Inicio'        => $ini ? $ini->format('d/m/Y H:i') : '—',
                'Fecha Fin'           => $fin ? $fin->format('d/m/Y H:i') : '—',
                'Huevos Eclosionados' => (int)($proceso->cantidad_eclosionados ?? 0),
                'Error Motor'         => (int)($err?->motor ?? 0),
                'Error Lámpara'       => (int)($err?->lampara ?? 0),
                'Error Sensor'        => (int)($err?->sensor ?? 0),
            ];
        });

        $headers = [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=avicultor_procesos_{$inc->codigo}_{$gestion}_".now()->format('Ymd_His').".xls",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($rows) {
            echo "<table border='1'>
                    <thead>
                      <tr>
                        <th>Incubadora</th>
                        <th>Nombre del Proceso</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Huevos Eclosionados</th>
                        <th>Error Motor</th>
                        <th>Error Lámpara</th>
                        <th>Error Sensor</th>
                      </tr>
                    </thead>
                    <tbody>";
            foreach ($rows as $r) {
                echo "<tr>
                        <td>{$r['Incubadora']}</td>
                        <td>{$r['Nombre del Proceso']}</td>
                        <td>{$r['Fecha Inicio']}</td>
                        <td>{$r['Fecha Fin']}</td>
                        <td>{$r['Huevos Eclosionados']}</td>
                        <td>{$r['Error Motor']}</td>
                        <td>{$r['Error Lámpara']}</td>
                        <td>{$r['Error Sensor']}</td>
                      </tr>";
            }
            if ($rows->isEmpty()) {
                echo "<tr><td colspan='8'>Sin datos para los filtros seleccionados.</td></tr>";
            }
            echo "</tbody></table>";
        };

        return response()->stream($callback, 200, $headers);
    }
}
