<?php

namespace App\Livewire\Perfil;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditarPerfil extends Component
{
    public $nombre = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        $this->nombre = Auth::user()->nombre ?? '';
    }

    public function guardar()
    {
        // OJO: tu tabla usa el campo 'contraseña' (no 'password')
        $rules = [
            'nombre'   => ['required','string','max:120'],
            'password' => ['nullable', Password::min(8), 'confirmed'],
        ];

        $this->validate($rules, [
            'nombre.required'    => 'El nombre es obligatorio.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $u = Auth::user();
        $u->nombre = $this->nombre;

        if (!empty($this->password)) {
            $u->contraseña = Hash::make($this->password); // <- campo real en tu BD
        }

        $u->save();

        // Limpia campos sensibles
        $this->reset('password','password_confirmation');

        // Cierra modal y muestra toast
        $this->dispatch('close-modal', id: 'modalPerfil');
        $this->dispatch('toast', ['tipo' => 'success', 'msg' => 'Datos actualizados correctamente.']);
    }

    public function render()
    {
        return view('livewire.perfil.editar-perfil');
    }
}
