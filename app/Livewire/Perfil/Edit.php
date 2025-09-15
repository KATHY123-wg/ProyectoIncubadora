<?php

namespace App\Livewire\Perfil;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class Edit extends Component
{
    public $usuario;
    public $password;
    public $password_confirmation;
    public $current_password;

    public function mount()
    {
        $this->usuario = Auth::user()->usuario;
    }

    public function guardar()
    {
        $user = Auth::user();

        // Determinar la tabla real del modelo para la regla unique
        $table = (new User)->getTable(); // evita hardcodear 'usuarios' vs 'users'

        // Validaciones
        $this->validate([
            'usuario'  => "required|string|max:60|unique:{$table},usuario," . $user->getKey(),
            'password' => [
                'nullable',
                'confirmed',
                // Política de complejidad (ajusta a tu criterio)
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],
            // Valida contra el guard actual (web). Si tu Admin usa otro guard, ver nota más abajo.
            'current_password' => ['required', 'current_password'],
        ]);

        // Verificar si realmente hay cambios
        $hayCambioUsuario = $this->usuario !== $user->usuario;
        $hayCambioPassword = filled($this->password);

        if (!$hayCambioUsuario && !$hayCambioPassword) {
            $this->dispatch('toast', [
                'tipo' => 'info',
                'msg'  => 'No hay cambios para guardar',
            ]);
            return;
        }

        // Aplicar cambios
        if ($hayCambioUsuario) {
            $user->usuario = $this->usuario;
        }

        if ($hayCambioPassword) {
            // Usa el nombre de columna real. Idealmente 'password'.
            // Si tu columna en BD se llama literalmente 'contraseña', cambia aquí:
            $user->password = Hash::make($this->password);
        }

        $user->modificado_por = $user->getKey();
        $user->save();

        // Rotar sesiones/tokens cuando se cambia la contraseña
        if ($hayCambioPassword) {
            // Cierra todas las demás sesiones activas en este guard (requiere current_password)
            Auth::logoutOtherDevices($this->current_password);

            // Si usas Sanctum/Passport y quieres revocar otros tokens de API:
            if (method_exists($user, 'tokens')) {
                $user->tokens()->where('name', '!=', 'current')->delete();
            }
        }

        // (Opcional) Si cambiaste el usuario (username), puedes regenerar la sesión actual
        // para minimizar riesgos de fijación de sesión:
        request()->session()->regenerate();

        $this->reset('password', 'password_confirmation', 'current_password');

        $this->dispatch('toast', [
            'tipo' => 'success',
            'msg'  => 'Perfil actualizado correctamente',
        ]);

        $this->dispatch('cerrar-modal');
    }

    public function render()
    {
        return view('livewire.perfil.edit');
    }
}
