@extends('admin.inicioadmin')

@section('titulo', 'Nosotros')
@section('titulo_nav', 'Administrador')

@section('contenido')
    <div style="font-family: 'Segoe UI', sans-serif; background: #edf2fa; padding: 30px; min-height: 100vh;">

        <h2 class="fw-bold text-center mb-4">HUEVSySTEM: Sistema de Incubación Web 📝</h2>

        <div class="container" style="max-width: 800px; margin: auto;">
            {{-- Sección Desarrolladores --}}
            <div class="card mb-4 shadow" style="border-radius: 12px;">
                <div class="card-body">
                    <h4 class="fw-bold">Desarrolladores</h4>
                    <p><strong>Ronald Pablo Moreira Gonzales</strong> – Técnico en Sistemas Informáticos</p>
                    <p><strong>Delia Soledad Lopez Segovia</strong> – Técnico en Sistemas Informáticos</p>
                    <p><strong>Katherine Lopez Ramos</strong> – Técnico en Sistemas Informáticos</p>
                </div>
            </div>

            {{-- Sección Objetivos y Beneficios --}}
            <div class="card mb-4 shadow" style="border-radius: 12px;">
                <div class="card-body">
                    <h4 class="fw-bold">Objetivos y Beneficios</h4>
                    <p>Este proyecto busca <strong>automatizar el monitoreo de incubadoras</strong> para optimizar la producción avícola en zonas rurales. Los beneficios son:</p>
                    <ul>
                        <li>✅ Ahorro de tiempo y mano de obra</li>
                        <li>✅ Monitoreo remoto en tiempo real</li>
                        <li>✅ Reducción de pérdidas por fallos no detectados</li>
                    </ul>
                </div>
            </div>

            {{-- Sección Contacto --}}
            <div class="card mb-4 shadow" style="border-radius: 12px;">
                <div class="card-body">
                    <h4 class="fw-bold">Contáctenos</h4>
                    <p>Email: <strong>HUEVSySTEM@gmail.com</strong></p>
                    <p>
                        <a href="https://wa.me/59173692762" target="_blank" style="text-decoration: none; color: #00796b;">
                            <i class="bi bi-whatsapp me-2"></i> WhatsApp: +591 73692762
                        </a>
                    </p>
                    <p>
                        <a href="https://www.instagram.com/miusuario" target="_blank" style="text-decoration: none; color: #00796b;">
                            <i class="bi bi-instagram me-2"></i> Instagram
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
