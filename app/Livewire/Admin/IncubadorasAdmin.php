<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Incubadora;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.base')]
class IncubadorasAdmin extends Component
{
    use WithPagination;

    // Filtros
    public $buscar = '';
    public $filtroEstado = '';
    public $filtroUsuario = null;

    // Formulario
    public $incubadoraId = null;
    public $codigo = '';
    public $descripcion = '';
    public $usuario_id = '';
    public $estado = 1;

    public $usuariosAvicultores = [];

    // Modal
    public $mostrarModal = false;
    // --- Asignación de incubadora a un usuario (NUEVO) ---

    // ===== Asignación de incubadoras (modal) =====
    public $usuarioAsignarId = null;
    public $usuarioAsignarNombre = null;
    public $incubadorasDisponibles = [];
    public $incubadoraSeleccionada = null;
    public $showAsignarModal = false;

    // ✅ Mensajes personalizados de validación (ES)
    protected $messages = [
        // código
        'codigo.required' => 'El código es obligatorio.',
        'codigo.max'      => 'El código no debe exceder 20 caracteres.',
        'codigo.regex'    => 'El código debe estar en MAYÚSCULAS y sin espacios.',
        'codigo.unique'   => 'Hay una incubadora con el mismo nombre.',

        // descripción
        'descripcion.required' => 'La descripción es obligatoria.',
        'descripcion.max'      => 'La descripción no debe superar 255 caracteres.',

        // usuario (si se usa)
        'usuario_id.integer' => 'El avicultor seleccionado no es válido.',
        'usuario_id.exists'  => 'El avicultor seleccionado no existe.',

        // estado
        'estado.required' => 'El estado es obligatorio.',
        'estado.in'       => 'El estado seleccionado no es válido.',
    ];

    // ✅ Nombres amigables de atributos (para mensajes)
    protected $validationAttributes = [
        'codigo'      => 'código',
        'descripcion' => 'descripción',
        'usuario_id'  => 'avicultor',
        'estado'      => 'estado',
    ];

    public function mount()
    {
        // Asegura que solo admin
        if (Auth::user()->rol !== 'admin') {
            abort(403);
        }
        // Si venimos desde crear avicultor con ?asignar_para=ID, abrir modal de asignación
        $asignar = request()->query('asignar_para');
        if ($asignar) {
            $this->usuarioAsignarId = (int) $asignar;
            $this->cargarDisponibles();         // carga incubadoras libres
            $this->showAsignarModal = true;
            $this->dispatch('abrir-modal-asignar'); // evento JS para abrir modal
        }

        $this->usuariosAvicultores = User::where('rol', 'avicultor')->orderBy('nombre')->get();
    }

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatingFiltroUsuario()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        $uniqueCodigo = 'unique:incubadoras,codigo';
        if ($this->incubadoraId) {
            $uniqueCodigo = 'unique:incubadoras,codigo,' . $this->incubadoraId . ',id';
        }

        return [
            // ✅ MAYÚSCULAS sin espacios (A-Z, 0-9, _ y -)
            'codigo'      => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9_-]+$/', $uniqueCodigo],
            'descripcion' => ['required', 'string', 'max:255'],
            // ✅ AHORA OPCIONAL (solo crear incubadora, sin asignar aún)
            'usuario_id'  => ['nullable', 'integer', 'exists:usuarios,id'],
            'estado'      => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function crear()
    {
        $this->resetForm();
        // Si ya está filtrando por un avicultor, puedes preseleccionarlo; si no, queda vacío (sin asignar)
        if ($this->filtroUsuario !== null && $this->filtroUsuario !== '') {
            $this->usuario_id = (int) $this->filtroUsuario;
        }
        $this->mostrarModal = true;
    }

    public function editar($id)
    {
        $inc = Incubadora::findOrFail($id);
        $this->incubadoraId = $inc->id;
        $this->codigo       = $inc->codigo;
        $this->descripcion  = $inc->descripcion;
        $this->usuario_id   = $inc->usuario_id;
        $this->estado       = (int)$inc->estado;

        $this->mostrarModal = true;
    }

    public function guardar()
    {
        // ✅ Normaliza a MAYÚSCULAS y sin espacios antes de validar
        $this->codigo = strtoupper(preg_replace('/\s+/', '', (string)$this->codigo));
        $this->descripcion = strtoupper((string)$this->descripcion);
        $this->validate();

        // ✅ ESTADO automático según asignación
        $asignada = !empty($this->usuario_id);

        $data = [
            'codigo'         => $this->codigo,
            'descripcion'    => $this->descripcion,
            'usuario_id'     => $asignada ? (int)$this->usuario_id : null, // null si no asignada
            'estado'         => $asignada ? 1 : 0, // Activa si asignada, Inactiva si no
            'modificado_por' => Auth::id(),
        ];

        Incubadora::create($data);

        $this->mostrarModal = false;
        $this->dispatch('toast', tipo: 'success', msg: 'Incubadora creada exitosamente.');
        $this->resetForm();
    }

    public function actualizar()
    {
        // ✅ Normaliza a MAYÚSCULAS y sin espacios antes de validar
        $this->codigo = strtoupper(preg_replace('/\s+/', '', (string)$this->codigo));

        $this->validate();

        $inc = Incubadora::findOrFail($this->incubadoraId);

        // ✅ ESTADO automático según asignación
        $asignada = !empty($this->usuario_id);

        $inc->update([
            'codigo'         => $this->codigo,
            'descripcion'    => $this->descripcion,
            'usuario_id'     => $asignada ? (int)$this->usuario_id : null, // null si se quita asignación
            'estado'         => $asignada ? 1 : 0, // se ajusta automáticamente
            'modificado_por' => Auth::id(),
        ]);

        $this->mostrarModal = false;
        $this->dispatch('toast', tipo: 'success', msg: 'Incubadora actualizada correctamente.');
        $this->resetForm();
    }

    public function cambiarEstado($id)
    {
        $inc = Incubadora::findOrFail($id);

        // ✅ Bloquea ACTIVAR si no tiene avicultor asignado
        if (!$inc->usuario_id && (int)$inc->estado === 0) {
            $this->dispatch('toast', [
                'tipo' => 'error',
                'msg'  => 'Asigna un avicultor para activar la incubadora.'
            ]);
            return;
        }

        $inc->estado = $inc->estado ? 0 : 1;
        $inc->modificado_por = Auth::id();
        $inc->save();

        // error
        $this->dispatch('toast', tipo: 'error', msg: 'Asigna un avicultor para activar la incubadora.');

        // éxito
        $this->dispatch('toast', tipo: 'success', msg: 'Estado actualizado.');
    }

    public function eliminar($id)
    {
        $inc = Incubadora::findOrFail($id);
        $inc->delete();
        $this->dispatch('toast', tipo: 'success', msg: 'Incubadora eliminada.');

        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['incubadoraId', 'codigo', 'descripcion', 'usuario_id', 'estado']);
        $this->estado = 1;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        $query = Incubadora::with('usuario')
            ->when($this->buscar, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('codigo', 'like', "%{$this->buscar}%")
                        ->orWhere('descripcion', 'like', "%{$this->buscar}%");
                });
            })
            ->when($this->filtroEstado !== '', fn($q) => $q->where('estado', (int)$this->filtroEstado))
            // 🔧 Asegurar que se aplique el filtro cuando haya valor no vacío
            ->when($this->filtroUsuario !== null && $this->filtroUsuario !== '', function ($q) {
                $q->where('usuario_id', (int) $this->filtroUsuario);
            })
            ->orderBy('id', 'desc');

        $incubadoras = $query->paginate(10);

        return view('livewire.admin.incubadoras-admin', compact('incubadoras'));
    }

    public function aplicarFiltroUsuario()
    {
        // Normaliza vacío a null (opcional, por prolijidad)
        if ($this->filtroUsuario === '') {
            $this->filtroUsuario = null;
        }
        $this->resetPage(); // para que empiece desde la página 1 con el filtro aplicado
    }
    // Abrir el modal de asignación manualmente (por si quieres un botón en la tabla)
    public function abrirAsignar($usuarioId)
    {
        $this->usuarioAsignarId = (int) $usuarioId;
        $this->cargarDisponibles();
        $this->showAsignarModal = true;
        $this->dispatch('abrir-modal-asignar');
    }

    // Cerrar modal de asignación
    public function cerrarAsignar()
    {
        $this->reset(['showAsignarModal', 'incubadoraSeleccionada']);
        $this->dispatch('cerrar-modal-asignar');
    }

    // Confirmar asignación (setear usuario_id en la incubadora)
    public function asignarIncubadora1()
    {
        $this->validate([
            'usuarioAsignarId'       => ['required', 'integer', 'exists:usuarios,id'],
            'incubadoraSeleccionada' => ['required', 'integer', 'exists:incubadoras,id'],
        ]);

        // Asegurar que sigue libre al momento de asignar
        $inc = Incubadora::whereNull('usuario_id')->findOrFail($this->incubadoraSeleccionada);

        $inc->usuario_id     = $this->usuarioAsignarId;
        $inc->modificado_por = Auth::id();
        $inc->save();

        $this->dispatch('toast', ['tipo' => 'success', 'msg' => 'Incubadora asignada correctamente.']);
        $this->cerrarAsignar();
    }

    private function cargarDisponibles()
    {
        $this->incubadorasDisponibles = Incubadora::query()
            ->where('estado', 0) // 👈 inactivas = disponibles
            ->where(function ($q) {
                $q->whereNull('usuario_id')
                    ->orWhere('usuario_id', 0); // por si guardan 0 en vez de null
            })
            ->orderBy('codigo')
            ->get();
    }

    public function asignarIncubadora()
    {
        $this->validate([
            'usuarioAsignarId'       => ['required', 'integer', 'exists:usuarios,id'],
            'incubadoraSeleccionada' => ['required', 'integer', 'exists:incubadoras,id'],
        ]);

        // Debe seguir inactiva y libre al momento de asignar
        $inc = Incubadora::where('estado', 0)
            ->where(function ($q) {
                $q->whereNull('usuario_id')
                    ->orWhere('usuario_id', 0);
            })
            ->findOrFail($this->incubadoraSeleccionada);

        $inc->usuario_id     = $this->usuarioAsignarId;
        $inc->estado         = 1;              // 👈 se activa al asignar
        $inc->modificado_por = Auth::id();
        $inc->save();

        // refresca (si tienes método propio, úsalo)
        $this->resetPage();

        $this->dispatch('toast', ['tipo' => 'success', 'msg' => 'Incubadora asignada correctamente.']);
        $this->cerrarAsignar();
    }
}
