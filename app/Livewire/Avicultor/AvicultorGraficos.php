<?php

namespace App\Livewire\Avicultor;

use Livewire\Component;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use App\Models\Incubadora;
use App\Models\Lectura_Sensores;
use App\Models\Incubacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AvicultorGraficos extends Component
{
    public $incubadoras;
    public $incubadoraId;
    public $gestion;
    public $mes;

    public $lineChartManana = null;
    public $lineChartNoche  = null;

    public $cargando = false;

    public function mount()
    {
        $this->incubadoras = Incubadora::where('usuario_id', Auth::id())
            ->select('id','codigo')
            ->orderBy('codigo')
            ->get();
    }

    public function generarDatos()
    {
        // Limpia gráficos si falta filtro
        if (!$this->incubadoraId || !$this->gestion || !$this->mes) {
            $this->lineChartManana = null;
            $this->lineChartNoche  = null;
            return;
        }

        $this->cargando = true;

        // 1) Obtén un proceso válido del usuario
        $procesoId = Incubacion::where('incubadora_id', $this->incubadoraId)
            ->whereHas('incubadora', fn($q) => $q->where('usuario_id', Auth::id()))
            ->value('id');

        if (!$procesoId) {
            $this->lineChartManana = null;
            $this->lineChartNoche  = null;
            $this->cargando = false;
            return;
        }

        // 2) Define rango de fechas del mes (reduce el set en SQL)
        $inicio = sprintf('%04d-%02d-01 00:00:00', $this->gestion, $this->mes);
        $fin    = date('Y-m-d H:i:s', strtotime($inicio.' +1 month'));

        // 3) Agrupa por minuto (o 5 min) para no mandar miles de puntos
        //    MySQL: DATE_FORMAT y AVG para suavizar
        $lecturasAgrupadas = Lectura_Sensores::where('proceso_id', $procesoId)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->selectRaw('
                DATE_FORMAT(fecha_hora, "%d %H:%i") as etiqueta,
                HOUR(fecha_hora) as h,
                AVG(temperatura) as temp,
                AVG(humedad) as hum
            ')
            ->groupBy('etiqueta','h')
            ->orderByRaw('MIN(fecha_hora) ASC')
            ->limit(1200) // seguridad dura; ajusta según carga
            ->get();

        // 4) Separa franjas en memoria (muy barato una vez reducido)
        $manana = [];
        $noche  = [];
        foreach ($lecturasAgrupadas as $r) {
            $et  = $r->etiqueta; // "dd HH:mm"
            $t   = (float) $r->temp;
            // $h   = (float) $r->hum; // por si luego quieres otra serie

            if ($r->h >= 6 && $r->h < 12) {
                $manana[] = ['x' => $et, 'y' => $t];
            } elseif ($r->h >= 18 && $r->h <= 23) {
                $noche[]  = ['x' => $et, 'y' => $t];
            }
        }

        // 5) Construye modelos de gráfico una sola vez
        $chartM = (new LineChartModel())
            ->setTitle('Temperatura Mañana')
            ->setAnimated(true);
        foreach ($manana as $p) {
            $chartM->addPoint($p['x'], $p['y']);
        }

        $chartN = (new LineChartModel())
            ->setTitle('Temperatura Noche')
            ->setAnimated(true);
        foreach ($noche as $p) {
            $chartN->addPoint($p['x'], $p['y']);
        }

        $this->lineChartManana = $chartM;
        $this->lineChartNoche  = $chartN;

        $this->cargando = false;
    }

    public function render()
    {
        // NO hacer consultas aquí; solo pasar lo ya calculado
        return view('livewire.avicultor.avicultor-graficos', [
            'lineChartManana' => $this->lineChartManana,
            'lineChartNoche'  => $this->lineChartNoche,
        ]);
    }
}
