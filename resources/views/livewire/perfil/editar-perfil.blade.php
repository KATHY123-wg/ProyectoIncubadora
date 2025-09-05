<div>
    {{-- Modal Bootstrap --}}
    <div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="modalPerfilLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background:#5D4037; color:#fff;">
                    <h5 class="modal-title" id="modalPerfilLabel">Editar perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" wire:model.defer="nombre">
                        @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Nueva contraseña (opcional)</label>
                        <input type="password" class="form-control" wire:model.defer="password" autocomplete="new-password">
                        @error('password') <small class="text-danger d-block">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" wire:model.defer="password_confirmation" autocomplete="new-password">
                    </div>

                    <div class="alert alert-info py-2">
                        Si no deseas cambiar la contraseña, deja los campos de contraseña vacíos.
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" wire:click="guardar" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm me-2"></span>
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Cerrar modal desde Livewire
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', ({id}) => {
                const m = bootstrap.Modal.getOrCreateInstance(document.getElementById(id));
                m.hide();
            });

            // SweetAlert2 para el toast si ya lo usas
            Livewire.on('toast', (data) => {
                if (window.Swal) {
                    Swal.fire({ icon: data.tipo || 'success', title: data.msg || 'Listo', timer: 2000, showConfirmButton: false });
                } else {
                    // Fallback simple
                    alert(data.msg || 'Operación realizada');
                }
            });
        });
    </script>
    @endpush
</div>
