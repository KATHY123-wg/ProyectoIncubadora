<?php

namespace App\Livewire\Vendedor;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\Detalle_venta;
use App\Models\Incubadora;

class Dashboard extends Component
{
    public int $range = 30; // días por defecto
    public $kpis = [
        'ventas_hoy' => 0,
        'ingresos_hoy' => 0.0,
        'ventas_mes' => 0,
        'incubadoras_libres' => 0,
    ];

    public array $chartVentasPorDia = [
        'categories' => [],
        'series' => [],
    ];

    public array $chartPorIncubadora = [
        'categories' => [],
        'series' => [],
    ];

    public function mount()
    {
        $this->cargarKPIs();
        $this->cargarGraficos();
    }

    public function updatedRange()
    {
        $this->cargarGraficos();
    }

    private function cargarKPIs(): void
    {
        $userId = Auth::id();
        $hoy = Carbon::now('America/La_Paz')->toDateString();

        // Ventas hoy e ingresos hoy (del vendedor logueado)
        $ventasHoy = Venta::whereDate('fecha_venta', $hoy)
            ->where('usuario_id', $userId);

        $this->kpis['ventas_hoy'] = (clone $ventasHoy)->count();
        $this->kpis['ingresos_hoy'] = (float) (clone $ventasHoy)->sum('total_bs');

        // Ventas del mes
        $inicioMes = Carbon::now('America/La_Paz')->startOfMonth();
        $finMes    = Carbon::now('America/La_Paz')->endOfMonth();

        $this->kpis['ventas_mes'] = Venta::whereBetween('fecha_venta', [$inicioMes, $finMes])
            ->where('usuario_id', $userId)
            ->count();

        // Incubadoras disponibles (no asignadas y estado=0)
        $this->kpis['incubadoras_libres'] = Incubadora::whereNull('usuario_id')
            ->where('estado', 0)
            ->count();
    }

    private function cargarGraficos(): void
    {
        $userId = Auth::id();
        $tz = 'America/La_Paz';

        $desde = Carbon::now($tz)->subDays($this->range - 1)->startOfDay();
        $hasta = Carbon::now($tz)->endOfDay();

        // Ventas por día (monto total por fecha)
        $rows = Venta::selectRaw("DATE(fecha_venta) as fecha, SUM(total_bs) as total")
            ->where('usuario_id', $userId)
            ->whereBetween('fecha_venta', [$desde, $hasta])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Construir eje X continuo
        $cursor = $desde->copy();
        $categories = [];
        $data = [];
        while ($cursor->lte($hasta)) {
            $d = $cursor->toDateString();
            $categories[] = $cursor->format('d/m');
            $match = $rows->firstWhere('fecha', $d);
            $data[] = $match ? (float)$match->total : 0.0;
            $cursor->addDay();
        }

        $this->chartVentasPorDia = [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Ingresos (Bs)',
                    'data' => $data,
                ]
            ],
        ];

        // Barras: ventas por incubadora (cantidad)
        $porInc = Detalle_venta::select('incubadora_id', DB::raw('COUNT(*) as cantidad'))
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->where('ventas.usuario_id', $userId)
            ->whereBetween('ventas.fecha_venta', [$desde, $hasta])
            ->groupBy('incubadora_id')
            ->orderByDesc('cantidad')
            ->limit(8)
            ->get();

        // Trae códigos de incubadoras en bloque
        $ids = $porInc->pluck('incubadora_id')->all();
        $mapCodigos = $ids
            ? Incubadora::whereIn('id', $ids)->pluck('codigo', 'id')
            : collect();

        $this->chartPorIncubadora = [
            'categories' => $porInc->map(fn($r) => $mapCodigos[$r->incubadora_id] ?? ('ID '.$r->incubadora_id))->all(),
            'series' => [
                [
                    'name' => 'Unidades vendidas',
                    'data' => $porInc->pluck('cantidad')->map(fn($n)=>(int)$n)->all(),
                ]
            ],
        ];

        // Enviar a JS para renderizar/actualizar charts
        $this->dispatch('charts:update', [
            'ventasPorDia'   => $this->chartVentasPorDia,
            'porIncubadora'  => $this->chartPorIncubadora,
        ]);
    }

    public function render()
    {
        // ventas recientes (tabla)
        $recientes = Venta::with(['detalleVentas.incubadora:id,codigo','avicultor:id,nombre,apellido1,apellido2'])
            ->where('usuario_id', Auth::id())
            ->latest('fecha_venta')
            ->limit(8)
            ->get();

        return view('livewire.vendedor.dashboard', [
            'recientes' => $recientes,
        ]);
    }
}
