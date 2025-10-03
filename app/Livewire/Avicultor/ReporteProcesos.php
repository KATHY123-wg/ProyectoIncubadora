<?php

namespace App\Livewire\Avicultor;

use App\Models\Incubacion;
use Livewire\Component;
use App\Models\Incubadora;
use App\Models\Lectura_Sensores;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ReporteProcesos extends Component
{
    public $incubadoras = [];
    public $incubadora_id;
    public $gestion;
    public $mes;   // opcional
    public $procesos = [];

    public function mount()
    {
        $this->incubadoras = Incubadora::where('usuario_id', Auth::id())
            ->orderBy('codigo')
            ->get();

        $this->procesos = [];
    }

    public function updatedIncubadoraId()
    {
        $this->generar();
    }
    public function updatedGestion()
    {
        $this->generar();
    }
    public function updatedMes()
    {
        $this->generar();
    }

    public function generar()
    {
        if (!$this->incubadora_id || !$this->gestion) {
            $this->procesos = [];
            return;
        }

        $q = Incubacion::with('incubadora:id,codigo')
            ->where('incubadora_id', $this->incubadora_id)
            ->whereYear('fecha_inicio', $this->gestion);

        if (!empty($this->mes)) {
            $q->whereMonth('fecha_inicio', $this->mes);
        }

        $items = $q->orderBy('fecha_inicio', 'desc')->get();

        $this->procesos = $items->map(function ($proceso) {
            $ini   = $proceso->fecha_inicio ? Carbon::parse($proceso->fecha_inicio) : null;
            $finRx = $proceso->fecha_fin ?? $proceso->fecha_fin_estimada ?? null;
            $fin   = $finRx ? Carbon::parse($finRx) : null;

            // 👇 OJO: la columna correcta es error_foco
            $err = Lectura_Sensores::selectRaw("
                SUM(CASE WHEN error_motor=1 THEN 1 ELSE 0 END) AS motor,
                SUM(CASE WHEN error_foco =1 THEN 1 ELSE 0 END) AS lampara,
                SUM(CASE WHEN error_sensor=1 THEN 1 ELSE 0 END) AS sensor
            ")
                ->where('proceso_id', $proceso->id)
                ->first();

            return [
                'codigo_incubadora'   => $proceso->incubadora->codigo ?? '-',
                'nombre'              => $proceso->nombre,
                'fecha_inicio'        => $ini ? $ini->format('d/m/Y H:i') : '—',
                'fecha_fin'           => $fin ? $fin->format('d/m/Y H:i') : '—',
                'huevos_eclosionados' => (int)($proceso->cantidad_eclosionados ?? 0),
                'errores_motor'       => (int)($err?->motor ?? 0),
                'errores_lampara'     => (int)($err?->lampara ?? 0), // alias conservado para tu Blade
                'errores_sensor'      => (int)($err?->sensor ?? 0),
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.avicultor.reporte-procesos');
    }
}
