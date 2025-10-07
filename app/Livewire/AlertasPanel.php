<?php

namespace App\Livewire;
use App\Models\Alerta;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AlertasPanel extends Component
{
    public $incubadora_id = null;

    /**
     * Guardamos el último conteo de críticas notificadas
     * para evitar disparar el toast en cada render/poll.
     */
    public $ultimaNotificacionCriticas = 0;

    public function mount()
    {
        // Opcional: si no quieres toast al cargar por primera vez,
        // inicializa con el conteo actual:
        $this->ultimaNotificacionCriticas = Alerta::where('estado', 'abierta')
            ->where(function ($q) {
                $q->whereNull('silenciada_hasta')->orWhere('silenciada_hasta', '<', now());
            })
            ->where('nivel', 'critical')
            ->when($this->incubadora_id, fn($q) => $q->where('incubadora_id', $this->incubadora_id))
            ->count();
    }
    public function render()
    {
        $user = Auth::user();

        $query = Alerta::with('incubadora', 'proceso')
            ->where('estado', 'abierta')
            ->where(function ($q) {
                $q->whereNull('silenciada_hasta')->orWhere('silenciada_hasta', '<', now());
            });

        // Rol avicultor: solo sus incubadoras
        if ($user->rol === 'avicultor') {
            $query->whereHas('incubadora', function ($q) use ($user) {
                $q->where('usuario_id', $user->id);
            });
        }

        // Rol vendedor: no ve alertas
        if ($user->rol === 'vendedor') {
            $alertas = collect();
        } else {
            $alertas = $query->orderByDesc('nivel')->orderByDesc('fecha_registro')->get();

            // === Disparo del toast solo para admin/avicultor ===
            $criticasActuales = $alertas->where('nivel', 'critical')->count();

            if ($criticasActuales > $this->ultimaNotificacionCriticas) {
                $this->dispatch('nuevaAlerta', [
                    'nivel' => 'critical',
                    'msg'   => 'Se detectó una alerta CRÍTICA. Revise el panel de alertas.'
                ]);
                $this->ultimaNotificacionCriticas = $criticasActuales;
            }
        }

        return view('livewire.alertas-panel', [
            'alertas' => $alertas
        ]);
    }


    public function resolver($id)
    {
        $alerta = Alerta::find($id);
        if ($alerta && $alerta->estado === 'abierta') {
            $alerta->update([
                'estado' => 'resuelta',
                'resuelta_en' => now(),
                'resuelta_por' => Auth::id(),
            ]);
            // Forzar refresco de tabla e indicador externo si lo usas
            $this->dispatch('alertasActualizadas');
        }
    }

    public function silenciar($id, $minutos = 60)
    {
        $alerta = Alerta::find($id);
        if ($alerta && $alerta->estado === 'abierta') {
            $alerta->update([
                'silenciada_hasta' => now()->addMinutes($minutos),
            ]);
            $this->dispatch('alertasActualizadas');
        }
    }
}
