<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\User;          // tabla 'usuarios'
use App\Models\Incubadora;
use App\Models\Venta;
use App\Models\Detalle_venta;

class VentaForm extends Component
{
    // Campos del formulario
    public $fecha_venta;
    public $buscador = '';
    public $sugerencias = [];
    public $avicultor_id = null;
    public $nombre_avicultor = '';

    public $incubadora_id = null;
    public $incubadoras = [];

    public $precio_bs = 1500;

    /** Autocomplete */
    public int  $focusIndex = -1;
    public bool $showSug    = false;

    public function mount()
    {
        // Fecha por defecto La Paz, Bolivia
        $this->fecha_venta = now('America/La_Paz')->format('Y-m-d\TH:i');

        // Solo incubadoras NO asignadas (usuario_id NULL y estado = 0)
        $this->incubadoras = Incubadora::query()
            ->whereNull('usuario_id')
            ->where('estado', 0)
            ->orderBy('codigo')
            ->get(['id', 'codigo'])
            ->toArray();
    }

    /** Buscar avicultor por nombre/apellidos/usuario/CI */
    public function updatedBuscador($value)
    {
        $txt = trim($value);

        // mientras escribe invalidamos la selección previa
        $this->avicultor_id = null;
        $this->nombre_avicultor = '';

        if (mb_strlen($txt) < 2) {
            $this->sugerencias = [];
            $this->showSug = false;
            $this->focusIndex = -1;
            return;
        }

        $rows = User::query()
            ->where('rol', 'avicultor')
            ->where(function ($w) use ($txt) {
                $w->where(DB::raw("CONCAT_WS(' ', nombre, apellido1, COALESCE(apellido2,''))"), 'like', "%{$txt}%")
                  ->orWhere('usuario', 'like', "%{$txt}%")
                  ->orWhere('ci_nit',  'like', "%{$txt}%");
            })
            ->orderBy('nombre')->orderBy('apellido1')
            ->limit(8)
            ->get(['id','nombre','apellido1','apellido2','ci_nit','usuario']);

        $this->sugerencias = $rows->map(function ($u) {
            $nombre = trim($u->nombre.' '.$u->apellido1.' '.($u->apellido2 ?? ''));
            $extra  = $u->ci_nit ?: $u->usuario;
            return [
                'id'    => (int)$u->id,
                'label' => $extra ? "{$nombre} ({$extra})" : $nombre,
            ];
        })->toArray();

        $this->showSug    = !empty($this->sugerencias);
        $this->focusIndex = $this->showSug ? 0 : -1;
    }

    /** Abrir lista con el botón 🔍 */
    public function abrirSugerencias()
    {
        $this->updatedBuscador($this->buscador);
        $this->showSug = !empty($this->sugerencias);
    }

    /** Selección con click/enter */
    public function seleccionarAvicultor($id)
    {
        $u = User::where('rol', 'avicultor')->findOrFail($id);

        $this->avicultor_id     = $u->id;
        $this->nombre_avicultor = trim($u->nombre.' '.$u->apellido1.' '.($u->apellido2 ?? ''));
        $this->buscador         = $this->nombre_avicultor.($u->ci_nit ? " ({$u->ci_nit})" : '');

        $this->sugerencias = [];
        $this->showSug     = false;
        $this->focusIndex  = -1;
    }

    /** Navegación con flechas */
    public function moveHighlight(int $dir): void
    {
        if (!$this->showSug || empty($this->sugerencias)) return;
        $n = count($this->sugerencias);
        $this->focusIndex = ($this->focusIndex + $dir + $n) % $n;
    }

    /** Enter aplica selección */
    public function selectHighlight(): void
    {
        if ($this->focusIndex >= 0 && isset($this->sugerencias[$this->focusIndex])) {
            $this->seleccionarAvicultor($this->sugerencias[$this->focusIndex]['id']);
        }
    }

    /** Limpiar selección (botón X) */
    public function limpiarAvicultor(): void
    {
        $this->buscador         = '';
        $this->avicultor_id     = null;
        $this->nombre_avicultor = '';
        $this->sugerencias      = [];
        $this->showSug          = false;
        $this->focusIndex       = -1;
    }

    /** Fallback: si escriben nombre/CI/usuario exacto y no abrieron la lista */
    private function ensureAvicultorSeleccionado(): void
    {
        if ($this->avicultor_id) return;

        $txt = trim($this->buscador);
        if ($txt === '') return;

        $match = User::where('rol','avicultor')
            ->where(function ($w) use ($txt) {
                $w->whereRaw("TRIM(CONCAT(nombre,' ',apellido1,' ',COALESCE(apellido2,''))) = ?", [$txt])
                  ->orWhere('ci_nit', $txt)
                  ->orWhere('usuario', $txt);
            })
            ->limit(2)
            ->get(['id','nombre','apellido1','apellido2']);

        if ($match->count() === 1) {
            $this->seleccionarAvicultor($match->first()->id);
        }
    }

    /** Guardar la venta (1 incubadora por venta) */
        /** Guardar la venta (1 incubadora por venta) */
 public function guardar()
    {
        // intenta resolver el avicultor automáticamente si coincide exacto
        $this->ensureAvicultorSeleccionado();

        $this->validate([
            'fecha_venta'   => 'required|date',
            'avicultor_id'  => 'required|integer|exists:usuarios,id',
            'incubadora_id' => 'required|integer|exists:incubadoras,id',
            'precio_bs'     => 'required|numeric|min:0.1',
        ], [
            'avicultor_id.required'  => 'Debe elegir un avicultor de la lista.',
            'incubadora_id.required' => 'Seleccione una incubadora.',
        ]);

        // Verifica que la incubadora siga libre
        $incubadoraLibre = Incubadora::where('id', $this->incubadora_id)
            ->whereNull('usuario_id')
            ->where('estado', 0)
            ->exists();

        if (!$incubadoraLibre) {
            $this->addError('incubadora_id', 'La incubadora ya no está disponible.');
            return;
        }

        DB::transaction(function () {
            // 1) Venta
            $venta = Venta::create([
                'avicultor_id'   => $this->avicultor_id,
                'usuario_id'     => Auth::id(),
                'fecha_venta'    => Carbon::parse($this->fecha_venta),
                'total_bs'       => $this->precio_bs,
                'estado'         => 1,
                'modificado_por' => Auth::id(),
            ]);

            // 2) Detalle (1 incubadora)
            Detalle_venta::create([
                'venta_id'        => $venta->id,
                'incubadora_id'   => $this->incubadora_id,
                'cantidad'        => 1,
                'precio_unitario' => $this->precio_bs,
            ]);

            // 3) Asignar incubadora al avicultor
            Incubadora::where('id', $this->incubadora_id)->update([
                'usuario_id'     => $this->avicultor_id,
                'estado'         => 1,
                'modificado_por' => Auth::id(),
            ]);
        });

        // refrescar combo
        $this->reset(['incubadora_id']);
        $this->incubadoras = Incubadora::whereNull('usuario_id')
            ->where('estado', 0)
            ->orderBy('codigo')->get(['id','codigo'])->toArray();
       $this->resetFormularioVenta();

        $this->dispatch('toast', [
        'tipo' => 'success',
        'msg'  => 'Se vendió correctamente la incubadora'
]);

    }
    public function render()
    {
        return view('livewire.admin.venta-form');
    }
    private function resetFormularioVenta()
{
    // Limpia estado del formulario
    $this->reset([
        'buscador',
        'showSug',
        'sugerencias',
        'focusIndex',
        'avicultor_id',
        'nombre_avicultor',
        'incubadora_id',
        'precio_bs',
    ]);

    // Fecha: déjala en "ahora" (o ponla en '' si prefieres vacía)
    $this->fecha_venta = now()->format('Y-m-d\TH:i');

    // Refresca combo de incubadoras libres
    $this->incubadoras = Incubadora::whereNull('usuario_id')
        ->where('estado', 0)
        ->orderBy('codigo')
        ->get(['id','codigo'])
        ->toArray();

    // Limpia mensajes de validación
    $this->resetValidation();

    // Enfoca el buscador otra vez
    $this->dispatch('focus', ['id' => 'inputBuscadorAvicultor']);
}

}
