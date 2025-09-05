<?php

namespace App\Livewire\Avicultor;
use Livewire\Component;
use Asantibanez\LivewireCharts\Models\LineChartModel;

use App\Models\Incubadora;
use App\Models\Lectura_Sensores;
use App\Models\Incubacion;

class AvicultorGraficos extends Component
{
    public $incubadoras;
    public $incubadoraId;
    public $gestion;
    public $mes;
    public $lineChartManana;
    public $lineChartNoche;

    public function mount()
    {
        $this->incubadoras = Incubadora::where('usuario_id', auth()->id())->get();
    }

    public function generarDatos()
{
    if (!$this->incubadoraId || !$this->gestion || !$this->mes) {
        $this->lineChartManana = null;
        $this->lineChartNoche = null;
        return;
    }

    $this->actualizarGraficos();
}


    public function actualizarGraficos()
    {
        $procesoId = Incubacion::where('incubadora_id', $this->incubadoraId)
            ->whereHas('incubadora', fn($q) => $q->where('usuario_id', auth()->id()))
            ->value('id');

        if (!$procesoId) {
            return;
        }

        $lecturas = Lectura_Sensores::where('proceso_id', $procesoId)
            ->whereYear('fecha_hora', $this->gestion)
            ->whereMonth('fecha_hora', $this->mes)
            ->get();

        $manana = [];
        $noche = [];

        foreach ($lecturas as $lectura) {
            $hora = date('H:i', strtotime($lectura->fecha_hora));
            $punto = [
                'hora' => date('d H:i', strtotime($lectura->fecha_hora)),
                'temperatura' => $lectura->temperatura,
                'humedad' => $lectura->humedad
            ];

            if ($hora >= '06:00' && $hora < '12:00') {
                $manana[] = $punto;
            } elseif ($hora >= '18:00' && $hora <= '23:59') {
                $noche[] = $punto;
            }
        }

        $this->lineChartManana = (new LineChartModel())
            ->setTitle('Temperatura Mañana')
            ->setAnimated(true);

        foreach ($manana as $p) {
            $this->lineChartManana->addPoint($p['hora'], $p['temperatura']);
        }

        $this->lineChartNoche = (new LineChartModel())
            ->setTitle('Temperatura Noche')
            ->setAnimated(true);

        foreach ($noche as $p) {
            $this->lineChartNoche->addPoint($p['hora'], $p['temperatura']);
        }
    }

    public function render()
{
    $lineChartManana = $this->crearGrafico('manana');
    $lineChartNoche = $this->crearGrafico('noche');

    return view('livewire.avicultor.avicultor-graficos', [
        'lineChartManana' => $lineChartManana,
        'lineChartNoche' => $lineChartNoche,
    ]);
}
public function crearGrafico($turno)
{
    $procesoId = Incubacion::where('incubadora_id', $this->incubadoraId)
        ->whereHas('incubadora', fn($q) => $q->where('usuario_id', auth()->id()))
        ->value('id');

    if (!$procesoId || !$this->gestion || !$this->mes) return null;

    $lecturas = Lectura_Sensores::where('proceso_id', $procesoId)
        ->whereYear('fecha_hora', $this->gestion)
        ->whereMonth('fecha_hora', $this->mes)
        ->get();

    $puntos = [];

    foreach ($lecturas as $lectura) {
        $hora = date('H:i', strtotime($lectura->fecha_hora));
        $punto = [
            'hora' => date('d H:i', strtotime($lectura->fecha_hora)),
            'temperatura' => $lectura->temperatura,
        ];

        if (
            ($turno === 'manana' && $hora >= '06:00' && $hora < '12:00') ||
            ($turno === 'noche' && $hora >= '18:00' && $hora <= '23:59')
        ) {
            $puntos[] = $punto;
        }
    }

    $titulo = $turno === 'manana' ? 'Temperatura Mañana' : 'Temperatura Noche';

    $chart = (new LineChartModel())
        ->setTitle($titulo)
        ->setAnimated(true);

    foreach ($puntos as $p) {
        $chart->addPoint($p['hora'], $p['temperatura']);
    }

    return $chart;
}


}
