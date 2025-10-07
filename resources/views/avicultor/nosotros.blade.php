@extends('avicultor.inicioavicultor')

@section('titulo', 'Nosotros')
@section('titulo_nav', 'Avicultor')

@section('contenido')
 <div class="container py-4">

    {{-- Hero --}}
    <div class="about-hero mb-4">
        <h2>HUEVSySTEM — Sistema de Incubación Web</h2>
        <p>Monitoreo y gestión inteligente para optimizar la producción avícola.</p>
    </div>

    <div class="row g-4">
        {{-- Desarrolladores --}}
        <div class="col-12">
            <div class="about-section">
                <div class="head">
                    <i class="bi bi-people-fill me-2"></i> Desarrolladores
                </div>
                <div class="body">
                    <div class="mb-2">
                        <strong>Ronald Pablo Moreira Gonzales</strong>
                        <span class="badge-role ms-2">Técnico en Sistemas Informáticos</span>
                    </div>
                    <div class="mb-2">
                        <strong>Delia Soledad Lopez Segovia</strong>
                        <span class="badge-role ms-2">Técnico en Sistemas Informáticos</span>
                    </div>
                    <div class="mb-1">
                        <strong>Katherine Lopez Ramos</strong>
                        <span class="badge-role ms-2">Técnico en Sistemas Informáticos</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Objetivos y beneficios --}}
        <div class="col-12">
            <div class="about-section">
                <div class="head">
                    <i class="bi bi-bullseye me-2"></i> Objetivos y Beneficios
                </div>
                <div class="body">
                    <p>
                        Este proyecto busca <strong>automatizar el monitoreo de incubadoras</strong> para optimizar la producción avícola en zonas rurales.
                    </p>
                    <div class="divider"></div>
                    <ul class="about-list">
                        <li>Reducción de tiempos operativos y mano de obra.</li>
                        <li>Monitoreo remoto en tiempo real y trazabilidad de procesos.</li>
                        <li>Menores pérdidas por fallos no detectados y decisiones informadas.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Contacto --}}
        <div class="col-12">
            <div class="about-section">
                <div class="head">
                    <i class="bi bi-envelope-paper me-2"></i> Contáctenos
                </div>
                <div class="body">
                    <p class="mb-2">Correo: <strong>HUEVSySTEM@gmail.com</strong></p>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="https://wa.me/59173692762" target="_blank" class="contact-link">
                            <i class="bi bi-whatsapp"></i> WhatsApp: <span class="fw-semibold">+591 73692762</span>
                        </a>

                        <a href="https://www.instagram.com/miusuario" target="_blank" class="contact-link">
                            <i class="bi bi-instagram"></i> Instagram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
