<div>
    <!-- Modal Bootstrap -->
    <div wire:ignore.self class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Editar perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form wire:submit.prevent="guardar" autocomplete="off">
                    <div class="modal-body position-relative">

                        <!-- Overlay de carga -->
                        <div wire:loading.flex wire:target="guardar"
                             class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75
                                    align-items-center justify-content-center" style="z-index: 10;">
                            <div class="spinner-border" role="status" aria-hidden="true"></div>
                            <span class="ms-2">Guardando…</span>
                        </div>

                        {{-- Usuario --}}
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" wire:model.defer="usuario" id="inp-usuario">
                            @error('usuario') <span class="text-danger small">{{ $message }}</span> @enderror
                            <div class="form-text">Máx. 60 caracteres. Debe ser único.</div>
                        </div>

                        {{-- Nueva contraseña --}}
                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" wire:model.defer="password" id="inp-pass">
                                <button class="btn btn-outline-secondary" type="button" id="btn-toggle-pass" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            <div class="form-text">Déjalo en blanco si no deseas cambiarla. Mínimo 6 caracteres.</div>
                        </div>

                        {{-- Confirmar contraseña --}}
                        <div class="mb-2">
                            <label class="form-label">Confirmar contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" wire:model.defer="password_confirmation" id="inp-pass2">
                                <button class="btn btn-outline-secondary" type="button" id="btn-toggle-pass2" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Contraseña actual (obligatoria para confirmar cambios) --}}
                        <div class="mb-3">
                            <label class="form-label">Contraseña actual</label>
                            <div class="input-group">
                                <input type="password" class="form-control" wire:model.defer="current_password" id="inp-current-pass" required>
                                <button class="btn btn-outline-secondary" type="button" id="btn-toggle-current" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                            <div class="form-text">Requerida para confirmar cualquier cambio de usuario o contraseña.</div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success"
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="guardar">
                            <span wire:loading wire:target="guardar" class="spinner-border spinner-border-sm me-2"></span>
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('modalPerfil');

        // Autofocus al abrir el modal
        modalEl?.addEventListener('shown.bs.modal', () => {
            const i = document.getElementById('inp-usuario');
            if (i) i.focus();
        });

        // Toggle ver/ocultar contraseña
        const toggle = (btnId, inputId) => {
            const btn = document.getElementById(btnId);
            const inp = document.getElementById(inputId);
            btn?.addEventListener('click', () => {
                const isPwd = inp.type === 'password';
                inp.type = isPwd ? 'text' : 'password';
                const icon = btn.querySelector('i');
                if (icon) icon.className = isPwd ? 'bi bi-eye-slash' : 'bi bi-eye';
                inp.focus();
            });
        };
        toggle('btn-toggle-pass',  'inp-pass');
        toggle('btn-toggle-pass2', 'inp-pass2');
        // Toggle para "Contraseña actual"
        toggle('btn-toggle-current', 'inp-current-pass');
    });
    </script>
    @endpush
   @if (session('mostrar_modal_credenciales'))
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const el   = document.getElementById('modalPerfil');
        const modo = @json(session('mostrar_modal_credenciales')); // 'forzado' | 'recordatorio'
        if (!el) return;

        // Avisar al componente Livewire en qué modo estamos (v3 y v2)
        if (window.Livewire) {
          if (typeof Livewire.dispatch === 'function') {
            Livewire.dispatch('set-modo', { modo: modo });     // v3
          } else if (typeof Livewire.emit === 'function') {
            Livewire.emit('set-modo', { modo: modo });         // v2
          }
        }

        // Configurar el modal según modo
        const modal = new bootstrap.Modal(el, {
          backdrop: (modo === 'forzado') ? 'static' : true,
          keyboard: (modo === 'forzado') ? false : true
        });
        modal.show();

        // Si es recordatorio y se cierra sin guardar → marcar revisión del mes
        el.addEventListener('hidden.bs.modal', function () {
          if (modo === 'recordatorio' && window.Livewire) {
            if (typeof Livewire.dispatch === 'function') {
              Livewire.dispatch('revision-credenciales-ok');   // v3
            } else if (typeof Livewire.emit === 'function') {
              Livewire.emit('revision-credenciales-ok');       // v2
            }
          }
        });

        // Ocultar botón "Cancelar" en modo forzado
        const btnCancelar = el.querySelector('.modal-footer .btn.btn-secondary');
        if (btnCancelar) btnCancelar.classList.toggle('d-none', (modo === 'forzado'));
    });
    </script>
    @endpush
@endif
</div>
