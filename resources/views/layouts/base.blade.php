<!-- resources/views/layouts/base.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head >
    @livewireStyles

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'HuevSystem')</title>
    <link rel="icon" href="{{ asset('start/assets/favicon.ico') }}">
    <link href="{{ asset('start/css/styles.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap CSS (debes tenerlo también en el <head>) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar personalizado según el rol -->
    @yield('sidebar')

    <!-- Contenido principal -->
    <div class="main-content">
        <nav class="navbar navbar-dark px-3 d-flex align-items-center justify-content-between">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>

            <span class="navbar-brand mb-0 h1">@yield('titulo_nav', 'Sistema de Incubación Web')</span>

            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-danger fw-bold d-flex align-items-center gap-2 btn-logout-hover">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </button>
            </form>
        </nav>

        <div class="content-wrapper">
            @yield('contenido')
        </div>

        <footer class="py-3 text-white text-center mt-auto shadow-sm">
            <div class="container">
                <p class="mb-1 fw-bold" style="font-size: 1.1rem;">
                    &copy; {{ date('Y') }} HUEVSySTEM - Comunidad de Apote
                </p>
                <small class="text-muted">Todos los derechos reservados.</small>
            </div>
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("active");
        }
    </script>
    <script src="{{ asset('start/js/scripts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Mostrar mensajes de sesión como modales "bonitos" --}}
    @if (session('success'))
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Operación exitosa',
        text: @json(session('success')),
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#6f7744' // tu color oliva
    });
    </script>
    @endif

    @if (session('error'))
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json(session('error')),
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#6f7744'
    });
    </script>
    @endif

    @if (session('info'))
    <script>
    Swal.fire({
        icon: 'info',
        title: 'Información',
        text: @json(session('info')),
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#6f7744'
    });
    </script>
    @endif

    {{-- 🔕 INHABILITADO: Preguntar asignación de incubadora al crear avicultor (se asigna en Ventas) --}}
    {{--
    @if (session('preguntar_incubadora'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const nombre = @json(session('nuevo_usuario_nombre'));
        const id     = @json(session('nuevo_usuario_id'));
        if (!id) return;

        Swal.fire({
            icon: 'question',
            title: 'Asignar incubadora',
            html: `El usuario <b>${nombre ?? ''}</b> fue registrado como avicultor.<br>¿Deseas asignarle una incubadora ahora?`,
            showCancelButton: true,
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'No, después',
            confirmButtonColor: '#6f7744', // tu color oliva
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Ruta a Gestión de Incubadoras con el usuario preseleccionado
                window.location.href = "{{ route('admin.incubadoras') }}" + "?asignar_para=" + encodeURIComponent(id);
            }
        });
    });
    </script>
    @endif
    --}}

    {{-- Preguntar si deseas desactivar también las incubadoras del usuario (Sí / No) --}}
    @if (session('preguntar_baja_inc'))
    <form id="frmDesactivarIncubs" action="{{ route('usuarios.desactivar-incubadoras', session('usuario_baja_id')) }}" method="POST" class="d-none">
        @csrf
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const nombre = @json(session('usuario_baja_nombre'));
        const count  = @json(session('incubadoras_activas'));

        Swal.fire({
            icon: 'question',
            title: 'Desactivar incubadoras',
            html: `El usuario <b>${nombre ?? ''}</b> tiene <b>${count}</b> incubadora(s) activa(s).<br>¿Deseas desactivarlas también?`,
            showCancelButton: true,
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'No, mantenerlas',
            confirmButtonColor: '#6f7744',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('frmDesactivarIncubs').submit();
            }
        });
    });
    </script>
    @endif

    {{-- Preguntar si deseas activar también las incubadoras del usuario (Sí / No) --}}
    @if (session('preguntar_alta_inc'))
    <form id="frmActivarIncubs" action="{{ route('usuarios.activar-incubadoras', session('usuario_alta_id')) }}" method="POST" class="d-none">
        @csrf
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const nombre = @json(session('usuario_alta_nombre'));
        const count  = @json(session('incubadoras_inactivas'));

        Swal.fire({
            icon: 'question',
            title: 'Activar incubadoras',
            html: `El usuario <b>${nombre ?? ''}</b> tiene <b>${count}</b> incubadora(s) inactiva(s).<br>¿Deseas activarlas también?`,
            showCancelButton: true,
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'No, mantenerlas',
            confirmButtonColor: '#198754', // success
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('frmActivarIncubs').submit();
            }
        });
    });
    </script>
    <script>
    Livewire.on('toast', (data) => {
        Swal.fire({
            icon: data.tipo,  // 'success', 'error', etc.
            title: data.msg,
            timer: 2000,
            showConfirmButton: false
        });
    });
</script>
    @endif
</body>

@livewireScripts
@once
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endonce
@stack('scripts')
</html>
