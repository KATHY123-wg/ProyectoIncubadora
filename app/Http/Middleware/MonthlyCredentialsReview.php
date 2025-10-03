<?php

// app/Http/Middleware/MonthlyCredentialsReview.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class MonthlyCredentialsReview
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $u = auth()->user();

            // 1) Password por defecto detectado (ajusta reglas)
            $passwordPorDefecto = false;
            try {
                if (Hash::check($u->ci_nit ?? '', $u->password ?? $u->contraseña ?? '')) $passwordPorDefecto = true;
                if (Hash::check($u->usuario ?? '', $u->password ?? $u->contraseña ?? '')) $passwordPorDefecto = true;
                if (Hash::check('123456',    $u->password ?? $u->contraseña ?? '')) $passwordPorDefecto = true;
            } catch (\Throwable $e) {
            }

            // 2) ¿Forzado?
            $forzado = ($u->requiere_cambio_password ?? 0) == 1
                || ($u->forzar_rotacion_credenciales ?? 0) == 1
                || $passwordPorDefecto;

            if ($forzado) {
                session()->flash('mostrar_modal_credenciales', 'forzado');
            } else {
                // 3) Recordatorio mensual
                $ultima = $u->ultima_revision_credenciales ? Carbon::parse($u->ultima_revision_credenciales) : null;
                $vence  = $ultima ? $ultima->copy()->addMonth() : null;
                if (is_null($ultima) || now()->greaterThanOrEqualTo($vence)) {
                    session()->flash('mostrar_modal_credenciales', 'recordatorio');
                }
            }
        }
        return $next($request);
    }
}
