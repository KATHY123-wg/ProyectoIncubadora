<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Incubadora;
use App\Models\Incubacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Livewire\Component;

class GraficosAdmin extends Component
{
    public $usuarios = [];
    public $usuario_id = null;
    public $filas = [];
    public $rango = 'realtime';

    public function mount()
    {
        
        // Solo admins
        if (Auth::user()?->rol !== 'admin') {
            abort(403);
        }

        // Obtener los usuarios avicultores
        $this->usuarios = User::where('rol', 'avicultor')
            ->orderBy('nombre')
            ->orderBy('apellido1')
            ->get();
    }

    /** Botón Cargar */
public function cargar()
{
    $this->validate([
        'usuario_id' => 'required|integer|exists:usuarios,id'
    ]);

    // Trae los procesos de las incubadoras asociadas al usuario
    $this->filas = Incubacion::query()
    ->join('incubadoras as i', 'i.id', '=', 'procesos.incubadora_id') // Cambiar 'incubaciones' por 'procesos'
    ->where('i.usuario_id', $this->usuario_id)
    ->select([
        'procesos.id',
        'i.codigo as incubadora_codigo',
        'procesos.nombre',
        'procesos.fecha_inicio',
        'procesos.fecha_estimada',
        'procesos.cantidad_total_huevos',
    ])
    ->orderByDesc('procesos.fecha_inicio') // Asegurarse de ordenar por la fecha de inicio de los procesos
    ->get()
    ->map(function ($r) {
        // Normaliza fechas
        $ini = $r->fecha_inicio ? Carbon::parse($r->fecha_inicio) : null;
        $fin = $r->fecha_estimada ? Carbon::parse($r->fecha_estimada) : null;
        $r->gestion_str = ($ini ? $ini->format('d/m/Y H:i') : '—') . ' — ' . ($fin ? $fin->format('d/m/Y H:i') : '—');
        
        return $r;
    });

    // Emitir gráficos una vez cargados los procesos
    $this->emitirChart();
}

public function emitirChart()
{
    // Verifica si se ha seleccionado un usuario
    if (!$this->usuario_id) {
        // Si no se selecciona usuario, no hay datos que mostrar
        $this->dispatch('grafData', type: 'bar', title: 'Sin datos', series: [], categories: []);
        return;
    }

    $now = Carbon::now();
    
    // Lógica para mostrar gráficos según el rango
    if ($this->rango === 'realtime') {
        // Gráfico de tiempo real: días restantes
        $rows = Incubacion::query()
            ->join('incubadoras as i', 'i.id', '=', 'procesos.incubadora_id') // Usar la tabla 'procesos' en lugar de 'incubaciones'
            ->where('i.usuario_id', $this->usuario_id) // Filtrar por el usuario seleccionado
            ->whereNotNull('fecha_inicio') // Asegurarse de que haya una fecha de inicio
            ->whereNotNull('fecha_estimada') // Asegurarse de que haya una fecha estimada
            ->where('fecha_inicio', '<=', $now) // Filtrar por fecha de inicio pasada
            ->where('fecha_estimada', '>=', $now) // Filtrar por fecha estimada futura
            ->select([
                'procesos.nombre as proc', // Cambiar 'incubaciones' por 'procesos'
                'i.codigo as inc',
                'procesos.fecha_inicio as ini', // Cambiar 'incubaciones' por 'procesos'
                'procesos.fecha_estimada as fin', // Cambiar 'incubaciones' por 'procesos'
            ])
            ->orderBy('ini')
            ->get();

        $cats = [];
        $data = [];

        foreach ($rows as $r) {
            $ini = Carbon::parse($r->ini); // Fecha de inicio del proceso
            $fin = Carbon::parse($r->fin); // Fecha estimada de fin del proceso
            $diasTot = max($ini->diffInDays($fin) ?: 1, 1); // Total de días entre inicio y fin
            $diasTrans = $ini->diffInDays($now); // Días transcurridos desde el inicio
            $diasRest = max($diasTot - $diasTrans, 0); // Días restantes (no negativos)

            $cats[] = "{$r->inc} - {$r->proc}"; // Categorías para los gráficos (incubadora - proceso)
            $data[] = $diasRest; // Días restantes para el gráfico
        }

        // Emitir los datos del gráfico para mostrarlo en la interfaz
        $this->dispatch('grafData',
            type: 'bar',
            title: 'Días restantes por proceso (en curso)', // Título del gráfico
            series: [['name' => 'Días restantes', 'data' => $data]], // Datos de los días restantes
            categories: $cats // Categorías de los gráficos
        );
    }
}


//public function emitirChart()
//{
  //  if (!$this->usuario_id) {
    //    $this->dispatch('grafData', type: 'bar', title: 'Sin datos', series: [], categories: []);
      //  return;
    //}

    //$now = Carbon::now();
    // Logica de gráficos según el rango (realtime, day, full)
//}


    public function render()
    {
        return view('livewire.admin.graficos-admin');
    }
}
