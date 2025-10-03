<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Incubadora;
use App\Models\Incubacion;

class ReporteProcesosAdmin extends Component
{
    public $usuarios;
    public $usuarioId = ''; // debe coincidir con wire:model
    public $incubadoras = [];
    public $incubadora_id = '';
    public $gestion = '';
    public $proceso = null;

    public function mount()
    {
        $this->usuarios = User::where('rol', 'avicultor')->get();
    }

    public function cargarIncubadoras()
    {
        $this->incubadora_id = '';

        if (empty($this->usuarioId)) {
            $this->incubadoras = [];
            $this->dispatch('alerta', mensaje: 'Primero selecciona un usuario.');
            return;
        }

        $this->incubadoras = Incubadora::where('usuario_id', (int)$this->usuarioId)
            ->orderBy('codigo')
            ->get();
    }
    public function updatedUsuarioId($value)
    {
        // Validar que el valor llegue
        logger("Usuario seleccionado: " . $value);

        // Consultar incubadoras del usuario
        $this->incubadoras = Incubadora::where('usuario_id', $value)->get();

        // Verificar si encontró incubadoras
        logger("Cantidad de incubadoras encontradas: " . $this->incubadoras->count());

        // Resetear otros campos
        $this->incubadora_id = '';
        $this->proceso = null;
    }

    public function buscarProceso()
    {
        $this->proceso = Incubacion::where('incubadora_id', $this->incubadora_id)
            ->whereYear('fecha_inicio', $this->gestion)
            ->first();

        if (!$this->proceso) return;

        $this->dispatch('renderProcesoChart', [
            'huevos_inicio' => $this->proceso->huevos_inicio,
            'huevos_eclosionados' => $this->proceso->huevos_eclosionados,
            'errores_motor' => $this->proceso->errores_motor,
            'errores_lampara' => $this->proceso->errores_lampara,
            'errores_sensor' => $this->proceso->errores_sensor,
        ]);
    }


    public function render()
    {
        return view('livewire.reporte-procesos-admin');
    }
}
