@extends('admin.inicioadmin')

@section('titulo', 'Nosotros')
@section('titulo_nav', 'Administrador')

@section('contenido')
<div class="container py-4">

    {{-- ====== SKIN local (puedes mover a tu layout con @push('styles')) ====== --}}
    <style>
        :root{
            --brand-primary:   #556B2F;  /* olivo oscuro */
            --brand-secondary: #6C7A3D;  /* olivo medio */
            --surface:         #FFF8E1;  /* crema */
            --accent:          #A1887F;  /* marrón claro/acento */
            --panel:           #5D4037;  /* marrón cabeceras */
            --ink:             #2b2b2b;
        }
        .about-hero{
            background: linear-gradient(135deg, var(--brand-secondary) 0%, #58613bff 100%);
            color: #fff;
            border-radius: 18px;
            padding: 28px 22px;
            box-shadow: 0 8px 20px rgba(0,0,0,.08);
        }
        .about-hero h2{
            font-weight: 800; letter-spacing: .4px;
            margin:0 0 6px 0; font-size: clamp(22px, 2.2vw, 30px);
        }
        .about-hero p{ margin:0; opacity:.95 }
        .about-section{
            border: 1px solid rgba(0,0,0,.06);
            border-radius:16px;
            background:#fff;
            box-shadow: 0 10px 24px rgba(0,0,0,.06);
        }
        .about-section .head{
            background: var(--surface);
            border-bottom: 1px solid rgba(0,0,0,.06);
            padding: 12px 16px;
            border-top-left-radius:16px; border-top-right-radius:16px;
            color: var(--brand-secondary);
            font-weight: 700;
        }
        .about-section .body{ padding: 16px 18px; color: var(--ink); }
        .about-list{ margin:0; padding-left: 1.1rem; }
        .about-list li{ margin:.25rem 0; }
        .badge-role{
            background:#efebe9; color:#4e342e; border:1px solid #d7ccc8;
            font-weight:600; border-radius:999px; padding:.25rem .6rem; font-size:.8rem;
        }
        .contact-link{
            text-decoration:none;
            display:inline-flex; align-items:center; gap:.5rem;
            color: var(--brand-secondary);
        }
        .contact-link:hover{ color: var(--panel); }
        .divider{ height:1px; background:rgba(0,0,0,.06); margin: 14px 0; }
    </style>

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
