<?php

namespace App\Livewire\Perfil;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PwRule;

class Edit extends Component
{
    public $usuario;
    public $password;
    public $password_confirmation;
    public $current_password;

    public $modo = 'normal'; // 'normal' | 'recordatorio' | 'forzado'

    protected $listeners = [
        'set-modo'                 => 'setModo',
        'revision-credenciales-ok' => 'marcarRevisionCredenciales',
    ];
    public function setModo($payload = null)  // <- AHORA OPCIONAL
    {
        // Acepta: ['modo' => 'forzado']  ó  'forzado'  ó  null
        if (is_array($payload)) {
            $this->modo = $payload['modo'] ?? 'normal';
        } else {
            $this->modo = $payload ?? 'normal';
        }
    }

    public function marcarRevisionCredenciales()
    {
        $u = Auth::user();
        if ($u) {
            $u->ultima_revision_credenciales = now();
            $u->save();
        }
    }

    public function mount()
    {
        $this->usuario = Auth::user()->usuario;
    }

    public function guardar()
    {
        $user  = Auth::user();
        $table = (new \App\Models\User)->getTable();

        // REGLAS: removemos la regla 'current_password' estándar y la validamos manualmente abajo
        $rules = [
            'usuario'  => "required|string|max:60|unique:{$table},usuario," . $user->getKey(),
            // 'current_password' => ['required', 'current_password'],  <- LA QUITAMOS
        ];

        // Si es forzado -> password OBLIGATORIA
        if ($this->modo === 'forzado') {
            $rules['password'] = [
                'required',
                'confirmed',
                PwRule::min(8)->letters()->mixedCase()->numbers()->uncompromised(),
            ];
        } else {
            $rules['password'] = [
                'nullable',
                'confirmed',
                PwRule::min(8)->letters()->mixedCase()->numbers()->uncompromised(),
            ];
        }

        // Validamos campos básicos (sin current_password aún)
        $this->validate($rules);

        // --- VALIDACIÓN MANUAL de current_password ---
        if (empty($this->current_password)) {
            $this->addError('current_password', 'La contraseña actual es obligatoria.');
            return;
        }

        $stored = $user->contraseña; // tu columna real

        $currentOk = false;
        try {
            // Si el hash coincide con Bcrypt/Hash::check -> OK
            if (Hash::check($this->current_password, $stored)) {
                $currentOk = true;
            } else {
                // Si el almacenado NO es un hash válido (p.ej. texto plano), permitimos la comparación directa
                // (esto cubre migraciones antiguas). Solo aceptar si EXACTAMENTE coinciden.
                if (trim($this->current_password) === trim((string)$stored)) {
                    $currentOk = true;
                }
            }
        } catch (\Throwable $e) {
            // En caso de error con Hash::check, intentamos la comparación directa por seguridad
            if (trim($this->current_password) === trim((string)$stored)) {
                $currentOk = true;
            }
        }

        if (!$currentOk) {
            $this->addError('current_password', 'La contraseña actual es incorrecta.');
            return;
        }

        // Si todo está OK: NORMALIZAR el hash almacenado a bcrypt si aún no lo estaba
        try {
            $info = Hash::info($stored);
            $algo = $info['algoName'] ?? null;
        } catch (\Throwable $e) {
            $algo = null;
        }
        if ($algo !== 'bcrypt') {
            // re-hash con la contraseña actual suministrada (ya validada)
            $user->contraseña = Hash::make($this->current_password);
            $user->save(); // guardamos temporalmente para que logoutOtherDevices funcione
        }

        // CONTINÚA con el resto de tu lógica original...
        // (a partir de aqui deja tu código: validaciones adicionales, asignaciones, etc.)

        // Validación adicional: que la nueva password NO sea igual a usuario/ci_nit/123456
        if (filled($this->password)) {
            $plain = $this->password;
            if (in_array($plain, [$user->usuario, $user->ci_nit ?? null, '123456'], true)) {
                $this->addError('password', 'La contraseña no puede ser tu usuario, CI/NIT ni "123456".');
                return;
            }
        }

        $hayCambioUsuario  = $this->usuario !== $user->usuario;
        $hayCambioPassword = filled($this->password);

        if (!$hayCambioUsuario && !$hayCambioPassword) {
            $this->dispatch('toast', ['tipo' => 'info', 'msg' => 'No hay cambios para guardar']);
            return;
        }

        if ($hayCambioUsuario) $user->usuario = $this->usuario;

        if ($hayCambioPassword) {
            // Ahora safe: cerramos otras sesiones usando current_password validado
            try {
                Auth::logoutOtherDevices($this->current_password);
            } catch (\Throwable $e) {
                // fallback: si usas session driver 'database' puedes limpiar sesiones manualmente
                if (config('session.driver') === 'database') {
                    try {
                        $sessionTable = config('session.table', 'sessions');
                        DB::table($sessionTable)
                            ->where('user_id', $user->getKey())
                            ->where('id', '!=', session()->getId())
                            ->delete();
                    } catch (\Throwable $inner) { /* noop */
                    }
                }
            }

            // asignamos la nueva contraseña (campo real 'contraseña')
            $user->contraseña = Hash::make($this->password);

            // Desactivar flags de fuerza
            $user->requiere_cambio_password     = 0;
            $user->forzar_rotacion_credenciales = 0;
            // Marcar revisión hecha hoy
            $user->ultima_revision_credenciales = now();
        }

        $user->modificado_por = $user->getKey();
        $user->save();

        if ($hayCambioPassword) {
            if (method_exists($user, 'tokens')) $user->tokens()->where('name', '!=', 'current')->delete();
        }

        request()->session()->regenerate();
        $this->reset('password', 'password_confirmation', 'current_password');

        $this->dispatch('toast', ['tipo' => 'success', 'msg' => 'Perfil actualizado correctamente']);
        $this->dispatch('cerrar-modal');
    }

    public function render()
    {
        return view('livewire.perfil.edit');
    }
}
