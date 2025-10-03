<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Carbon;

class ReporteVentasAdmin extends Component
{
    public $usuarios;
    public $usuario_id = '';
    public $gestion = '';

    // ⚠️ Eliminado: public $mes = '';
    public $nombreCajero = null;

    // La usaremos para totales mensuales (antes “por día” al tener mes)
    public $ventasPorDia = [];
    public $ventas;

    public function mount()
    {
        $this->usuarios = User::where('rol', 'vendedor')->get();
    }

    public function generar()
    {
        // Validación mínima
        if (!$this->usuario_id || !$this->gestion) return;

        // ▶️ Quitado el filtro por mes: trae TODO el año seleccionado
        $query = Venta::with(['detalleVentas.incubadora', 'avicultor', 'cajero'])
            ->where('usuario_id', $this->usuario_id)
            ->whereYear('fecha_venta', $this->gestion)
            ->orderBy('fecha_venta', 'asc');

        $this->ventas = $query->get();

        // Nombre del cajero (vendedor)
        $usuario = User::find($this->usuario_id);
        $this->nombreCajero = $usuario ? ($usuario->nombre . ' ' . $usuario->apellido1) : 'Desconocido';

        // ▶️ Agrupar por MES dentro de la gestión seleccionada
        // Clave '01'..'12' => total Bs del mes
        $porMes = $this->ventas->groupBy(function ($venta) {
            return Carbon::parse($venta->fecha_venta)->format('m');
        })->map(function ($group) {
            return $group->sum('total_bs');
        });

        // Construimos 12 puntos (meses) para el gráfico, aunque algún mes no tenga ventas
        $mesesCortos = [1 => 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $labels = [];
        $montos = [];

        for ($i = 1; $i <= 12; $i++) {
            $key = str_pad($i, 2, '0', STR_PAD_LEFT);
            $labels[] = $mesesCortos[$i];
            $montos[] = (float) ($porMes[$key] ?? 0);
        }

        // Guardamos por si lo necesitas en la vista (opcional)
        $this->ventasPorDia = $porMes->toArray(); // ahora realmente es por mes

        // Enviar datos al frontend para el gráfico (no cambié nombres de claves)
        $this->dispatch('renderVentasChart', [
            'dias'    => $labels,   // ← ahora son meses (Ene, Feb, …)
            'montos'  => $montos,
        ]);
    }

    public function render()
    {
        return view('livewire.reporte-ventas-admin');
    }
}
