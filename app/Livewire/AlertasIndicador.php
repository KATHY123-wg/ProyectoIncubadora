<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Alerta;
use Illuminate\Support\Facades\Auth;

class AlertasIndicador extends Component
{
    public $criticas = 0;
    public $rol;

    public function render()
    {
        $user = Auth::user();
        $this->rol = $user->rol;

        // Solo Admin y Avicultor ven alertas
        if ($user->rol === 'admin') {
            $this->criticas = Alerta::where('estado','abierta')
                ->where(function($q){
                    $q->whereNull('silenciada_hasta')->orWhere('silenciada_hasta','<', now());
                })
                ->where('nivel','critical')
                ->count();
        } elseif ($user->rol === 'avicultor') {
            $this->criticas = Alerta::where('estado','abierta')
                ->where(function($q){
                    $q->whereNull('silenciada_hasta')->orWhere('silenciada_hasta','<', now());
                })
                ->where('nivel','critical')
                ->whereHas('incubadora', function($q) use ($user){
                    $q->where('usuario_id', $user->id);
                })
                ->count();
        } else {
            $this->criticas = 0;
        }

        return view('livewire.alertas-indicador');
    }
}
