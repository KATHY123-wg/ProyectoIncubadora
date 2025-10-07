<div wire:poll.5s>
    @if($rol !== 'vendedor')
        <a href="{{ route('alertas.panel') }}"
           class="position-relative d-inline-block"
           title="Alertas">
           
           {{-- Icono campana --}}
           <i class="bi bi-bell-fill"
              style="font-size: 1.4rem;
                     color: {{ $criticas > 0 ? ' #dc3545' : '#edf2fa' }};
                     @if($criticas > 0) animation: pulse 1s infinite; @endif">
           </i>

           {{-- Badge con número de críticas --}}
           @if($criticas > 0)
               <span class="position-absolute top-0 start-100 translate-middle
                            badge rounded-pill bg-danger text-white">
                   {{ $criticas }}
               </span>
           @endif
        </a>

        <style>
        @keyframes pulse {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        </style>
    @endif
</div>
