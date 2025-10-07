<?php

namespace App\Livewire\Avicultor;

use App\Models\Incubacion; // ⬅️ usa tu modelo real
use App\Models\Incubadora;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\WithPagination;

class ProcesoForm extends Component
{
    /** ⬇️ NUEVO: límites del proceso */
    private const DIAS_ESTIMADOS = 21;
    private const DIAS_MAXIMO    = 23;

    public $incubadoras = [];
    public $incubadora_id;
    public $nombre;
    public $fecha_inicio;
    public $cantidad_total_huevos;
    public $observaciones;
    public $fecha_eclosion_estimada = null;

    // 🔹 Listado y toggles
    public $procesos = [];
    public $mostrarFormulario = false;
    public $modoEdicion = false;
    public $proceso_id = null;
    //
   use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    /** 🔔 Mensajes claros de validación */
    protected $messages = [
        'incubadora_id.required' => 'Debe seleccionar una incubadora.',
        'incubadora_id.exists'   => 'La incubadora seleccionada no es válida.',
        'nombre.required'        => 'El nombre del proceso es obligatorio.',
        'nombre.not_regex'       => 'El nombre no puede estar vacío ni contener solo espacios.',
        'nombre.max'             => 'El nombre del proceso no debe exceder 50 caracteres.',
        'nombre.regex'           => 'El nombre solo puede contener MAYÚSCULAS, números, espacios y - . _',
        'fecha_inicio.required'  => 'La fecha de inicio es obligatoria.',
        'fecha_inicio.date'      => 'La fecha de inicio no tiene un formato válido.',
        'cantidad_total_huevos.required' => 'La cantidad de huevos es obligatoria.',
        'cantidad_total_huevos.integer'  => 'La cantidad de huevos debe ser un número entero.',
        'cantidad_total_huevos.min'      => 'La cantidad de huevos debe ser al menos 1.',
        'cantidad_total_huevos.between'  => 'La cantidad de huevos maxima es 35.',
    ];

    public function mount()
    {
        $this->incubadoras  = Incubadora::where('usuario_id', Auth::id())->get();
        $this->fecha_inicio = now('America/La_Paz')->format('Y-m-d\TH:i');
        $this->cargarProcesos(); // carga inicial (si hay incubadora preseleccionada)
    }

    public function updatedIncubadoraId()
    {
         $this->resetPage();
    }

    /** 🔧 Normaliza nombre: colapsa espacios y convierte a MAYÚSCULAS */
    private function normalizarNombre(): void
    {
        $n = (string) $this->nombre;
        $n = preg_replace('/\s+/', ' ', $n); // colapsa espacios múltiples
        $n = trim($n);
        $this->nombre = mb_strtoupper($n, 'UTF-8');
    }

    /** 🧪 Nombre sugerido tipo “PROCESO N” */
    protected function sugerirNombre(): string
    {
        if (!$this->incubadora_id) return 'PROCESO';

        $n = Incubacion::where('incubadora_id', $this->incubadora_id)->count();
        return 'PROCESO ' . ($n + 1);
    }


    // Cargar procesos de la incubadora seleccionada
    public function cargarProcesos()
    {
        if (!$this->incubadora_id) {
            $this->procesos = collect(); // evita errores cuando no hay selección
            return;
        }

        $this->procesos = Incubacion::query()
            ->where('incubadora_id', $this->incubadora_id)
            ->select(['id','nombre','fecha_inicio','fecha_estimada','cantidad_total_huevos'])
            ->orderByDesc('fecha_inicio')
            ->paginate($this->perPage);
    }


    // Mostrar formulario de creación (ahora en modal)
    public function irACrear()
    {
        $this->mostrarFormulario = true;
        $this->modoEdicion = false;
        $this->resetValidation();
        $this->reset(['cantidad_total_huevos', 'observaciones', 'fecha_eclosion_estimada']);

        // nombre sugerido tipo “PROCESO N”
        $this->nombre = $this->sugerirNombre();

        $this->fecha_inicio = now()->format('Y-m-d\TH:i');

        // 🟢 ABRIR MODAL
        $this->dispatch('abrir-modal-proceso');
    }

    public function cancelarCrear()
    {
        $this->mostrarFormulario = false;
        $this->modoEdicion = false;
        $this->proceso_id = null;

        $this->reset(['nombre', 'fecha_inicio', 'cantidad_total_huevos', 'observaciones', 'fecha_eclosion_estimada']);
        $this->fecha_inicio = now()->format('Y-m-d\TH:i');
        $this->resetValidation();

        // 🔴 CERRAR MODAL
        $this->dispatch('cerrar-modal-proceso');
    }

    public function iniciarProceso()
    {
        // Normaliza a MAYÚSCULAS antes de validar
        $this->normalizarNombre();

        // Si el nombre quedó vacío, sugiere uno (estilo Windows)
        if ($this->nombre === '' || preg_match('/^\s*$/', $this->nombre)) {
            $this->nombre = $this->sugerirNombre();
        }

        $this->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'nombre'        => ['required', 'string', 'max:50', 'not_regex:/^\s*$/', 'regex:/^(?=.*\S)[A-Z0-9\s\-\._]+$/'],
            'fecha_inicio'  => [
                'required',
                'date',
                /** ⬇️ NUEVO: no permitir pasado */
                function ($attribute, $value, $fail) {
                    try {
                        $inicio = Carbon::parse($value);
                    } catch (\Throwable $e) {
                        return; // el validador 'date' ya se encarga del formato inválido
                    }
                    if ($inicio->lt(now()->startOfMinute())) {
                        $fail('La fecha de inicio no puede estar en el pasado.');
                    }
                },
            ],
            // entero 1..35 (sin 0, sin negativos, sin decimales)
            'cantidad_total_huevos' => ['required', 'integer', 'between:1,35'],
            'observaciones'         => 'nullable|string'
        ]);

        $inicio = Carbon::parse($this->fecha_inicio);

        // Fin estimado dentro del máximo permitido (21 por defecto, tope 23)
        $dias = min(self::DIAS_ESTIMADOS, self::DIAS_MAXIMO);
        $finEst = $inicio->copy()->addDays($dias);

        Incubacion::create([
            'incubadora_id'         => (int)$this->incubadora_id,
            'nombre'                => $this->nombre, // ya MAYÚSCULAS
            'fecha_inicio'          => $inicio,
            'fecha_estimada'    => $finEst,
            'cantidad_total_huevos' => (int)$this->cantidad_total_huevos,
            'observaciones'         => $this->observaciones,
            'modificado_por'        => Auth::id(),
        ]);

        $this->fecha_eclosion_estimada = $finEst->format('d/m/Y');

        // Refresca la tabla y oculta el form
        $this->cargarProcesos();

        // 🔴 CERRAR MODAL
        //$this->dispatch('cerrar-modal-proceso');

        $this->cancelarCrear(); // limpia estado

        // Toast opcional
        $this->dispatch('toast', ['tipo' => 'success', 'msg' => 'Proceso creado.']);
    }

    public function editarProceso($id)
    {
        $proc = Incubacion::findOrFail($id);
        $prevInc = $this->incubadora_id;
        // Asegura que la incubadora del proceso sea la seleccionada
        $this->incubadora_id = $proc->incubadora_id;

        // Precarga campos (nombre en MAYÚSCULAS)
        $this->proceso_id = $proc->id;
        $this->nombre = mb_strtoupper((string)$proc->nombre, 'UTF-8');
        $this->fecha_inicio = optional($proc->fecha_inicio)->format('Y-m-d\TH:i');
        $this->cantidad_total_huevos = $proc->cantidad_total_huevos;
        $this->observaciones = $proc->observaciones;

        $this->modoEdicion = true;
        $this->mostrarFormulario = true;

         if ($prevInc !== $this->incubadora_id) {
        $this->cargarProcesos();
    }
        // 🟢 ABRIR MODAL
        $this->dispatch('abrir-modal-proceso');
    }

    public function actualizarProceso()
    {
        // Normaliza a MAYÚSCULAS antes de validar
        $this->normalizarNombre();

        $this->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'nombre'        => ['required', 'string', 'max:50', 'not_regex:/^\s*$/', 'regex:/^(?=.*\S)[A-Z0-9\s\-\._]+$/'],
            'fecha_inicio'  => [
                'required',
                'date',
                /** ⬇️ NUEVO: no permitir pasado */
                function ($attribute, $value, $fail) {
                    try {
                        $inicio = Carbon::parse($value);
                    } catch (\Throwable $e) {
                        return;
                    }
                    if ($inicio->lt(now()->startOfMinute())) {
                        $fail('La fecha de inicio no puede estar en el pasado.');
                    }
                },
            ],
            // entero 1..35
            'cantidad_total_huevos' => ['required', 'integer', 'between:1,35'],
            'observaciones'         => 'nullable|string'
        ]);

        $proc   = Incubacion::findOrFail($this->proceso_id);
        $inicio = Carbon::parse($this->fecha_inicio);

        // Fin estimado dentro del máximo permitido (21 por defecto, tope 23)
        $dias = min(self::DIAS_ESTIMADOS, self::DIAS_MAXIMO);
        $finEst = $inicio->copy()->addDays($dias);

        $proc->update([
            'incubadora_id'         => (int)$this->incubadora_id,
            'nombre'                => $this->nombre, // MAYÚSCULAS
            'fecha_inicio'          => $inicio,
            'fecha_estimada'    => $finEst,
            'cantidad_total_huevos' => (int)$this->cantidad_total_huevos,
            'observaciones'         => $this->observaciones,
            'modificado_por'        => Auth::id(),
        ]);

        // Feedback
        $this->fecha_eclosion_estimada = $finEst->format('d/m/Y');

        // Refresca y limpia
        $this->cargarProcesos();

        // 🔴 CERRAR MODAL
        $this->dispatch('cerrar-modal-proceso');

        $this->cancelarCrear();

        $this->dispatch('toast', ['tipo' => 'success', 'msg' => 'Proceso actualizado.']);
    }

    public function eliminarProceso($id)
    {
        $proc = Incubacion::findOrFail($id);
        $proc->delete();

        $this->cargarProcesos();
        $this->dispatch('toast', ['tipo' => 'success', 'msg' => 'Proceso eliminado.']);
    }
    public function render()
{
    $procesos = collect();
    if ($this->incubadora_id) {
        $procesos = Incubacion::query()
            ->where('incubadora_id', $this->incubadora_id)
            ->select(['id','nombre','fecha_inicio','fecha_estimada','cantidad_total_huevos'])
            ->orderByDesc('fecha_inicio')
            ->paginate($this->perPage);
    }

    return view('livewire.avicultor.proceso-form', [
        'procesos' => $procesos,
    ]);
}

}
