@extends('layouts.base')

@section('titulo', 'Dashboard Admin')
@section('titulo_nav', 'Administrador')

@section('sidebar')
<div class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="{{ asset('images/logo (2).png') }}" alt="Logo" class="img-fluid">
        <h6 class="mt-2">ADMINISTRADOR</h6>

        <div style="font-size: 14px; margin-top: 8px; color: #5D4037; font-weight: bold;">
             <a href="#" data-bs-toggle="modal" data-bs-target="#modalPerfil"
            onclick="event.preventDefault();"
            style="text-decoration:none; color:#5D4037; font-weight:bold; font-size:14px;">
            👤 {{ Auth::user()->nombre }}
        </a>
         </div>
    </div>

    <a class="nav-link btn-sidebar" href="{{ route('admin.inicio') }}">
        <i class="bi bi-house-door me-2 menu-icon"></i> Inicio
    </a>
    <a class="nav-link" href="{{ route('admin.usuarios') }}">
        <i class="bi bi-people-fill me-2 menu-icon"></i> Gestión de Usuarios
    </a>
    <a class="nav-link" href="{{ route('admin.incubadoras') }}">
        <i class="bi bi-egg-fried me-2"></i> Gestión de Incubadoras
    </a>
    <a class="nav-link" href="{{ route('admin.graficos') }}">
        <i class="bi bi-bar-chart-line me-2 menu-icon"></i> Gráficos
    </a>
    <a class="nav-link" href="{{ route('admin.ventas') }}">
        <i class="bi bi-cash-stack me-2 menu-icon"></i> Ventas
    </a>
    <div class="sidebar-dropdown">
        <a class="nav-link btn-sidebar d-flex justify-content-between align-items-center" href="#">
            <span><i class="bi bi-file-earmark-text me-2 menu-icon"></i> Reportes</span>
            <i class="bi bi-caret-down-fill"></i>
        </a>
        <div class="submenu">
           <a href="{{ route('reportes.ventas') }}" class="nav-link sub-link">
                <i class="bi bi-file-earmark-bar-graph me-2 menu-icon"></i> Reportes de Ventas
            </a>

            <a href="{{ route('reportes.procesos') }}" class="nav-link sub-link">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes de Procesos
            </a>
        </div>
    </div>
    <a class="nav-link" href="{{ route('admin.nosotros') }}">
        <i class="bi bi-people me-2 menu-icon"></i> Nosotros
    </a>
    
</div>
@endsection

@section('contenido')
 

<div id="bgVideoWrap" aria-hidden="true">
  <video
    id="bgVideo"
    playsinline
    webkit-playsinline
    muted
    loop
    autoplay
    preload="auto"
    poster="{{ asset('images/incubacion.png') }}"
  >
    <source src="{{ asset('videos/incubadora.webm') }}" type="video/webm">
    <source src="{{ asset('videos/incubadora.mp4') }}" type="video/mp4">
    Tu navegador no soporta video HTML5.
  </video>
  <div id="bgDim"></div>
</div>
{{-- ======= /VIDEO DE FONDO ======= --}}

<h2 class="mb-4 fw-bold">Panel del Administrador</h2>

<!-- 📌 Tarjetas tipo botón con íconos estilo profesional -->
<section class="pt-4">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <!-- Tarjeta: Incubadoras disponibles / vendidas -->
            <div class="col-md-5">
                <a href="{{ route('admin.ventas') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature rounded-3 mb-3" style="background-color: #6f7744; color: white;">
                                <i class="bi bi-box-seam fs-2"></i>
                            </div>
                            <h5 class="card-title text-dark">Incubadoras disponibles / vendidas</h5>
                            <p class="card-text text-secondary">Consulta el estado de tu inventario de incubadoras.</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Tarjeta: Usuarios registrados -->
            <div class="col-md-5">
                <a href="{{ route('admin.usuarios') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature rounded-3 mb-3" style="background-color: #6f7744; color: white;">
                                <i class="bi bi-people fs-2"></i>
                            </div>
                            <h5 class="card-title text-dark">Usuarios registrados</h5>
                            <p class="card-text text-secondary">Gestión de usuarios activos en el sistema.</p>
                        </div>
                    </div>
                </a>
            </div>
            
        </div>
    </div>
<style>
  /* ===== Fondo de video (mínimo cambio) ===== */
  html, body{
  height:100%;
  background: transparent !important;  /* <- antes era #f4f6f9 */
  color:#1f2a37;
}


  /* El video va pegado al viewport y detrás de todo */
 #bgVideoWrap{ z-index: 0; }     /* <- antes -2 */
 #bgDim{ z-index: 1; }           /* <- antes -1 */

  #bgVideo{
    position:absolute; top:0; left:0;
    width:100%; height:100%;
    object-fit: cover; display:block;
  }
  /* Oscurecido para contraste SIN tapar contenido */

  /* ===== Mantén tu UI por encima y sin “planchas” que tapen el video ===== */
  /* Cualquier wrapper común que suele traer fondo, lo dejamos transparente */
  .main-content,
  .content,
  .content-wrapper,
  .page-content,
  .container,
  .container-fluid,
  section,
  main,
  #content{
    background: transparent !important;
  }


  /* Navbar y Sidebar: iguales que tenías (sólidos y por encima) */
  .navbar{ z-index:10; background:hsl(69, 27%, 37%)!important; color:#fff; }
  #sidebar{ z-index:9;  background:#a0a887; }

:root{
  --blue-gray: 237, 242, 250;   /* valores RGB de #edf2fa */
}

.card{
  background: rgba(var(--blue-gray), 0.70); /* mismo tono, 80% opaco */
  backdrop-filter: blur(4px);               /* mantiene legibilidad */
  border: 1px solid rgba(0,0,0,0.08);
}

  .card-title{ color:#1f2a37; }
  .card-text{  color:#4b5563; }
  .text-dark{ color:#1f2a37 !important; }
  .text-secondary{ color:#4b5563 !important; }

  /* Modales por encima de todo */
  .modal{ z-index:2000 !important; }
  .modal-backdrop{ z-index:1990 !important; }
  /* ===== Footer siempre visible ===== */
footer {
  position: relative;
  z-index: 5;                        /* más que el video/velo */
  background: #6f7744 !important;    /* color sólido (igual que tu navbar) */
  color: #fff !important;
  text-align: center;
  padding: 10px 0;
}

/* Variante con transparencia si prefieres ver el video detrás */
footer.transparent {
  background: #6f7744 !important; /* café con opacidad */
}

</style>
</section>

{{-- Forzar autoplay si el navegador lo bloquea sin interacción --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const v = document.getElementById('bgVideo');
  if (!v) return;
  const tryPlay = () => {
    const pr = v.play?.();
    if (pr && typeof pr.then === 'function') {
      pr.catch(() => {
        v.muted = true;
        v.play().catch(()=>{});
      });
    }
  };
  tryPlay();
  window.addEventListener('focus', tryPlay);
});
</script>
   <script>
     Livewire.on('cerrar-modal', () => {
     const modal = bootstrap.Modal.getInstance(document.getElementById('modalPerfil'));
      if (modal) modal.hide();
     });
    </script>


@endsection
