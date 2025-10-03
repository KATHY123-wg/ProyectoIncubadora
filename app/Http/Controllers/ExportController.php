<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Incubadora;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /* =========================
       FILTROS COMUNES (opcionales)
       ========================= */
    private function filtrarUsuarios(Request $request)
    {
        return User::query()
            ->when($request->filled('rol'), fn($q) => $q->where('rol', $request->rol))
            ->when($request->filled('estado'), fn($q) => $q->where('estado', (int)$request->estado))
            ->orderBy('apellido1')
            ->orderBy('nombre')
            ->get([
                'id','nombre','apellido1','apellido2','usuario','correo','rol','ci_nit','telefono','direccion','estado'
                // OJO: sin 'created_at'
            ]);
    }

    private function filtrarIncubadoras(Request $request)
    {
        return Incubadora::query()
            ->with(['usuario:id,nombre,apellido1,apellido2'])
            ->when($request->filled('estado'), fn($q) => $q->where('estado', (int)$request->estado))
            ->when($request->filled('usuario_id'), fn($q) => $q->where('usuario_id', (int)$request->usuario_id))
            ->orderBy('codigo')
            ->get([
                'id','codigo','descripcion','usuario_id','estado','modificado_por'
                // OJO: sin 'created_at'
            ]);
    }

    /* =========================
       USUARIOS - PDF
       ========================= */
    public function usuariosPDF(Request $request)
    {
        $usuarios = $this->filtrarUsuarios($request);
        $pdf = Pdf::loadView('exports.usuarios-pdf', compact('usuarios'))
                  ->setPaper('a4', 'portrait');

        $nombre = 'usuarios_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($nombre);
    }

    /* =========================
       USUARIOS - CSV
       ========================= */
    public function usuariosCSV(Request $request): StreamedResponse
    {
        $usuarios = $this->filtrarUsuarios($request);
        $fileName = 'usuarios_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-Type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$fileName}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        // Sin columna "Creado"
        $columns = ['ID','Nombre completo','Usuario','Correo','Rol','CI/NIT','Teléfono','Dirección','Estado'];

        $callback = function () use ($usuarios, $columns) {
            $out = fopen('php://output', 'w');
            // BOM para Excel (evita acentos mal)
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, $columns);

            foreach ($usuarios as $u) {
                $nombreCompleto = trim($u->nombre.' '.$u->apellido1.' '.($u->apellido2 ?? ''));
                fputcsv($out, [
                    $u->id,
                    $nombreCompleto,
                    $u->usuario,
                    $u->correo,
                    $u->rol,
                    $u->ci_nit,
                    $u->telefono,
                    $u->direccion,
                    $u->estado ? 'Activo' : 'Inactivo',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* =========================
       USUARIOS - XLS (HTML table)
       ========================= */
    public function usuariosXLS(Request $request): StreamedResponse
    {
        $usuarios = $this->filtrarUsuarios($request);
        $filename = 'usuarios_' . now()->format('Ymd_His') . '.xls';

        $headers = [
            "Content-Type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        // Sin "Creado"
        $columns = ['ID','Nombre completo','Usuario','Correo','Rol','CI/NIT','Teléfono','Dirección','Estado'];

        $callback = function () use ($usuarios, $columns) {
            echo "<meta charset='UTF-8'>";
            echo "<table border='1' cellspacing='0' cellpadding='4'><thead><tr>";
            foreach ($columns as $c) echo "<th>{$c}</th>";
            echo "</tr></thead><tbody>";

            foreach ($usuarios as $u) {
                $nombreCompleto = trim($u->nombre.' '.$u->apellido1.' '.($u->apellido2 ?? ''));
                echo "<tr>
                    <td>{$u->id}</td>
                    <td>".e($nombreCompleto)."</td>
                    <td>".e($u->usuario)."</td>
                    <td>".e($u->correo)."</td>
                    <td>".e($u->rol)."</td>
                    <td>".e($u->ci_nit)."</td>
                    <td>".e($u->telefono)."</td>
                    <td>".e($u->direccion)."</td>
                    <td>".($u->estado ? 'Activo' : 'Inactivo')."</td>
                </tr>";
            }
            echo "</tbody></table>";
        };

        return response()->stream($callback, 200, $headers);
    }

    /* =========================
       INCUBADORAS - PDF
       ========================= */
    public function incubadorasPDF(Request $request)
    {
        $incubadoras = $this->filtrarIncubadoras($request);
        $pdf = Pdf::loadView('exports.incubadoras-pdf', compact('incubadoras'))
                  ->setPaper('a4', 'portrait');

        $nombre = 'incubadoras_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($nombre);
    }

    /* =========================
       INCUBADORAS - CSV
       ========================= */
    public function incubadorasCSV(Request $request): StreamedResponse
    {
        $incubadoras = $this->filtrarIncubadoras($request);
        $fileName = 'incubadoras_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-Type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$fileName}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        // Sin "Creada"; agrego "Descripción"
        $columns = ['ID','Código','Descripción','Asignada a','Estado','Modificado por'];

        $callback = function () use ($incubadoras, $columns) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, $columns);

            foreach ($incubadoras as $i) {
                $duenio = $i->usuario
                    ? trim($i->usuario->nombre.' '.$i->usuario->apellido1.' '.($i->usuario->apellido2 ?? ''))
                    : '—';
                fputcsv($out, [
                    $i->id,
                    $i->codigo,
                    $i->descripcion,
                    $duenio,
                    $i->estado ? 'Activa' : 'Inactiva',
                    $i->modificado_por ?? '—',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* =========================
       INCUBADORAS - XLS (HTML table)
       ========================= */
    public function incubadorasXLS(Request $request): StreamedResponse
    {
        $incubadoras = $this->filtrarIncubadoras($request);
        $filename = 'incubadoras_' . now()->format('Ymd_His') . '.xls';

        $headers = [
            "Content-Type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        // Sin "Creada"; con "Descripción"
        $columns = ['ID','Código','Descripción','Asignada a','Estado','Modificado por'];

        $callback = function () use ($incubadoras, $columns) {
            echo "<meta charset='UTF-8'>";
            echo "<table border='1' cellspacing='0' cellpadding='4'><thead><tr>";
            foreach ($columns as $c) echo "<th>{$c}</th>";
            echo "</tr></thead><tbody>";

            foreach ($incubadoras as $i) {
                $duenio = $i->usuario
                    ? trim($i->usuario->nombre.' '.$i->usuario->apellido1.' '.($i->usuario->apellido2 ?? ''))
                    : '—';

                echo "<tr>
                    <td>{$i->id}</td>
                    <td>".e($i->codigo)."</td>
                    <td>".e($i->descripcion)."</td>
                    <td>".e($duenio)."</td>
                    <td>".($i->estado ? 'Activa' : 'Inactiva')."</td>
                    <td>".e($i->modificado_por ?? '—')."</td>
                </tr>";
            }

            echo "</tbody></table>";
        };

        return response()->stream($callback, 200, $headers);
    }
}
