@extends('layouts.app')

@section('title', 'Fases del Proyecto')

@section('content')
<style>
    /* ============================================
       MOBILE FIRST - Fases y Tareas
       ============================================ */
    
    .actividades-header {
        background: transparent;
        padding: 0.75rem 0.25rem;
        border-radius: 0;
        margin-bottom: 0.75rem;
        box-shadow: none;
    }

    .actividades-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .actividades-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .actividades-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: #0D6EFD;
        color: white;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    .header-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }

    .btn-action {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-volver {
        background: #6c757d;
        color: white;
    }

    .btn-volver:hover {
        background: #5a6268;
        color: white;
    }

    .btn-colaboradores {
        background: #E7F1FF;
        color: #0D6EFD;
    }

    .btn-colaboradores:hover {
        background: #D0E7FF;
        color: #0D6EFD;
    }

    .btn-add-activity {
        background: #0D6EFD;
        color: white;
    }

    .btn-add-activity:hover {
        background: #0B5ED7;
        color: white;
    }

    /* Barra de progreso */
    .fase-progress {
        margin-top: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .fase-progress-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.375rem;
        font-size: 0.75rem;
        color: #6c757d;
    }

    .fase-progress-bar-container {
        width: 100%;
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .fase-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #0D6EFD 0%, #0B5ED7 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .fase-progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(
            90deg,
            transparent 0%,
            rgba(255, 255, 255, 0.2) 50%,
            transparent 100%
        );
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }

    .fase-progress-bar.completed {
        background: linear-gradient(90deg, #198754 0%, #157347 100%);
    }

    .fase-progress-bar.warning {
        background: linear-gradient(90deg, #FFC107 0%, #E0A800 100%);
    }

    .fase-progress-percentage {
        font-weight: 600;
        color: #212529;
    }

    /* Fase/Actividad Card */
    .fase-card {
        background: #ffffff;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.3s ease-out, padding 0.3s ease-out;
        padding: 0.5rem;
    }

    .fase-card.expanded {
        background: #ffffff;
        padding: 0.75rem;
        transition: background-color 0.4s ease-in, padding 0.4s ease-in;
    }

    .fase-header {
        padding: 0.5rem 0.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: none;
        transition: background 0.2s;
    }

    .fase-header:hover {
        background: #f8f9fa;
    }

    .fase-header.expanded {
        border-bottom: 2px solid #0D6EFD;
    }

    .fase-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border-radius: 0;
        color: #6c757d;
        font-size: 1rem;
    }

    .fase-info {
        flex: 1;
    }

    .fase-title {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .fase-meta {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .fase-toggle {
        color: #6c757d;
        font-size: 1.25rem;
        transition: transform 0.3s;
    }

    .fase-toggle.expanded {
        transform: rotate(180deg);
    }

    /* Dropdown de estado inline */
    .dropdown-estado-inline {
        position: relative;
        display: inline-block;
    }

    .badge-clickable {
        cursor: pointer;
        user-select: none;
        display: inline-flex;
        align-items: center;
    }

    .badge-clickable:hover {
        opacity: 0.8;
    }

    .dropdown-menu-estado {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        min-width: 180px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        margin-top: 4px;
        padding: 4px 0;
    }

    .dropdown-item-estado {
        padding: 8px 16px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .dropdown-item-estado:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item-estado.active {
        background-color: #e7f3ff;
    }

    .badge-estado-item {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Date picker inline */
    .fecha-clickable {
        cursor: pointer;
        user-select: none;
        padding: 4px;
        border-radius: 4px;
        transition: background-color 0.2s;
        position: relative;
    }

    .fecha-clickable:hover {
        background-color: #f8f9fa;
    }

    .date-picker-inline {
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 12px;
        margin-top: 4px;
        min-width: 250px;
    }

    .date-picker-content {
        display: flex;
        flex-direction: column;
    }

    td {
        position: relative;
    }

    /* Dropdown de estado inline */
    .dropdown-estado-inline {
        position: relative;
        display: inline-block;
    }

    .badge-clickable {
        cursor: pointer;
        user-select: none;
        display: inline-flex;
        align-items: center;
    }

    .badge-clickable:hover {
        opacity: 0.8;
    }

    .dropdown-menu-estado {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        min-width: 180px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        margin-top: 4px;
        padding: 4px 0;
    }

    .dropdown-item-estado {
        padding: 8px 16px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .dropdown-item-estado:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item-estado.active {
        background-color: #e7f3ff;
    }

    .badge-estado-item {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Date picker inline */
    .fecha-clickable {
        cursor: pointer;
        user-select: none;
        padding: 4px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .fecha-clickable:hover {
        background-color: #f8f9fa;
    }

    .date-picker-inline {
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 12px;
        margin-top: 4px;
        min-width: 250px;
    }

    .date-picker-content {
        display: flex;
        flex-direction: column;
    }

    /* Fecha vencida - resaltar en rojo */
    .fecha-vencida {
        color: #dc3545 !important;
        font-weight: 600;
    }

    .fecha-vencida i {
        color: #dc3545 !important;
    }

    /* Tareas dentro de la fase */
    .tareas-container {
        max-height: 0;
        overflow: hidden;
        padding: 0;
        transition: max-height 0.4s ease-out, padding 0.4s ease-out;
    }

    .tareas-container.show {
        max-height: 5000px;
        padding: 0.5rem 0;
        transition: max-height 0.5s ease-in, padding 0.4s ease-in;
    }

    .avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
        border: 2px solid white;
        z-index: 1;
        position: relative;
    }

    .avatar-small:hover {
        z-index: 2;
        transform: scale(1.1);
        transition: transform 0.2s;
    }

    .tarea-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }

    .badge-prioridad-alta {
        background: #F8D7DA;
        color: #721C24;
    }

    .badge-prioridad-media {
        background: #CFE2FF;
        color: #084298;
    }

    .badge-prioridad-baja {
        background: #E2E3E5;
        color: #41464B;
    }

    .badge-estado-completado {
        background: #D1E7DD;
        color: #0F5132;
    }

    .badge-estado-progreso,
    .badge-estado-en_progreso {
        background: #FFC107 !important;
        color: #000000 !important;
        font-weight: 600;
        border: 1px solid #FFB300;
    }

    .badge-estado-pendiente {
        background: #E2E3E5;
        color: #41464B;
    }

    .tarea-asignados {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
        background: #0D6EFD;
    }

    .avatar-overlap {
        margin-left: -8px;
    }

    .avatar-overlap:first-child {
        margin-left: 0;
    }

    .tarea-fecha {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: #6c757d;
    }

    .tarea-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-edit {
        color: #6c757d;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .btn-edit:hover {
        background: #f8f9fa;
        color: #0D6EFD;
    }

    /* Formulario de crear tarea */
    .form-crear-tarea {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table th {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        font-size: 0.875rem;
        vertical-align: middle;
    }

    /* Checkboxes de tareas - estilo igual a Mis Tareas Independientes */
    .tarea-checkbox,
    .form-check-input.tarea-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        flex-shrink: 0;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: white;
        position: relative;
        margin: 0;
    }

    .tarea-checkbox:checked,
    .form-check-input.tarea-checkbox:checked {
        border-radius: 4px;
        background: #6f42c1;
        border-color: #6f42c1;
    }

    .tarea-checkbox:checked::after,
    .form-check-input.tarea-checkbox:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 11px;
        font-weight: bold;
        line-height: 1;
    }

    /* Checkbox select-all también con el mismo estilo */
    input[type="checkbox"][id^="select-all-"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        flex-shrink: 0;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: white;
        position: relative;
        margin: 0;
    }

    input[type="checkbox"][id^="select-all-"]:checked {
        border-radius: 4px;
        background: #6f42c1;
        border-color: #6f42c1;
    }

    input[type="checkbox"][id^="select-all-"]:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 11px;
        font-weight: bold;
        line-height: 1;
    }

    /* ============================================
       TABLET STYLES (768px and up)
       ============================================ */
    @media (min-width: 768px) {
        .actividades-header {
            padding: 0.75rem 0.5rem;
        }

        .actividades-title {
            font-size: 1.5rem;
        }

        .header-actions {
            margin-top: 1rem;
            justify-content: flex-end;
        }

        .fase-header {
            padding: 0.5rem 0.5rem;
        }

        .table th {
            font-size: 0.8125rem;
        }

        .table td {
            font-size: 0.9375rem;
        }
    }

    /* ============================================
       DESKTOP STYLES (992px and up)
       ============================================ */
    @media (min-width: 992px) {
        .actividades-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .header-title-section {
            flex: 1;
        }

        .header-actions {
            margin-top: 0;
        }

        .table {
            font-size: 0.9375rem;
        }
    }

    /* ============================================
       PANEL LATERAL DE EVIDENCIAS DE TAREA
       ============================================ */
    .panel-evidencias-tarea-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        display: none;
    }

    .panel-evidencias-tarea-overlay.show {
        display: block;
    }

    .panel-evidencias-tarea {
        position: fixed;
        top: 0;
        right: -100%;
        width: 100%;
        max-width: 500px;
        height: 100%;
        background: white;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        transition: right 0.3s ease;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        font-size: 0.8rem; /* Tamaño base reducido */
    }

    .panel-evidencias-tarea.show {
        right: 0;
    }

    .panel-evidencias-tarea-header {
        padding: 0.875rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .panel-evidencias-tarea-header h4 {
        font-size: 1.1em;
        margin: 0;
    }

    .panel-evidencias-tarea-header .btn-close {
        font-size: 1.25em;
        width: 1.5em;
        height: 1.5em;
    }

    .panel-evidencias-tarea-body {
        padding: 0.875rem;
        flex: 1;
    }

    .panel-evidencias-tarea-section {
        margin-bottom: 1.875rem;
    }

    /* Ajustar tamaños de inputs y textareas dentro del panel */
    .panel-evidencias-tarea .form-control {
        font-size: 1em;
        padding: 0.5rem 0.75rem;
    }

    .panel-evidencias-tarea textarea.form-control {
        font-size: 1em;
        padding: 0.5rem 0.75rem;
        line-height: 1.5;
    }

    /* Ajustar tamaños de botones dentro del panel */
    .panel-evidencias-tarea .btn {
        font-size: 0.875em;
        padding: 0.5rem 0.75rem;
    }

    .panel-evidencias-tarea .btn-sm {
        font-size: 0.8125em;
        padding: 0.375rem 0.625rem;
    }

    .panel-evidencias-tarea .btn i {
        font-size: 1em;
    }

    .panel-evidencias-tarea-section h5 {
        font-size: 1.125em;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 0.5em;
    }

    .panel-evidencias-tarea-section h5 i {
        font-size: 1.25em;
    }

    .descripcion-box {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.5rem;
        margin-bottom: 0.625rem;
        min-height: 3.75rem;
        display: flex;
        align-items: center;
    }

    .descripcion-box p {
        margin: 0;
        color: #6c757d;
        font-size: 1em;
        line-height: 1.5;
    }

    .btn-editar-descripcion {
        color: #0D6EFD;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3125rem;
        font-size: 0.875em;
        margin-top: 0.5rem;
    }

    .btn-editar-descripcion i {
        font-size: 1em;
    }

    .btn-editar-descripcion:hover {
        text-decoration: underline;
    }

    .comentarios-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5em;
        height: 1.5em;
        background: #6c757d;
        color: white;
        border-radius: 50%;
        font-size: 0.75em;
        font-weight: 600;
        margin-left: 0.5em;
    }

    .comentarios-empty {
        text-align: center;
        padding: 1.5rem 0;
        color: #6c757d;
    }

    .comentarios-empty i {
        font-size: 2.5em;
        opacity: 0.3;
        margin-bottom: 0.5rem;
    }

    .comentarios-empty p {
        font-size: 1em;
        margin: 0;
    }

    .comentario-input-wrapper {
        position: relative;
        margin-top: 0.75rem;
    }

    .comentario-input-wrapper input {
        padding-right: 5rem;
        font-size: 1em;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        padding-left: 0.75rem;
    }

    .comentario-input-icons {
        position: absolute;
        right: 0.625rem;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .comentario-input-icons button {
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 0.25rem;
        font-size: 1.125em;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .comentario-input-icons button i {
        font-size: 1em;
    }

    .comentario-input-icons button:hover {
        color: #0D6EFD;
    }

    .btn-agregar-comentario {
        width: 100%;
        margin-top: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5em;
        font-size: 0.875em;
        padding: 0.5rem 0.75rem;
    }

    .btn-agregar-comentario i {
        font-size: 1em;
    }

    .evidencias-empty {
        text-align: center;
        padding: 0.75rem 0;
        color: #6c757d;
        margin-bottom: 0.75rem;
    }

    .evidencias-empty p {
        font-size: 1em;
        margin: 0;
    }

    .drop-zone {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #f8f9fa;
    }

    .drop-zone:hover {
        border-color: #0D6EFD;
        background: #e7f1ff;
    }

    .drop-zone.dragover {
        border-color: #0D6EFD;
        background: #e7f1ff;
    }

    .drop-zone i {
        font-size: 2.5em;
        color: #0D6EFD;
        margin-bottom: 0.5rem;
    }

    .drop-zone-text {
        color: #0D6EFD;
        font-weight: 500;
        margin-bottom: 0.25rem;
        font-size: 1em;
    }

    .drop-zone-subtext {
        font-size: 0.875em;
        color: #6c757d;
    }

    .comentario-item-tarea, .evidencia-item-tarea {
        background: #f8f9fa;
        padding: 0.625rem;
        border-radius: 0.375rem;
        margin-bottom: 0.625rem;
        border-left: 3px solid #0D6EFD;
    }

    .comentario-item-tarea > div:last-child,
    .evidencia-item-tarea > div:first-child {
        font-size: 1em;
        line-height: 1.5;
    }

    .comentario-item-tarea .usuario-info,
    .evidencia-item-tarea .usuario-info {
        font-size: 0.875em;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .evidencia-item-tarea .evidencia-acciones {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .evidencia-item-tarea .evidencia-acciones button {
        font-size: 0.875em;
        padding: 0.375rem 0.5rem;
    }

    .evidencia-item-tarea .evidencia-acciones button i {
        font-size: 1em;
    }

    @media (max-width: 768px) {
        .panel-evidencias-tarea {
            max-width: 100%;
        }
    }

    /* ============================================
       ESTILOS DEL CALENDARIO - Diseño Moderno
       ============================================ */
    .calendario-container {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    /* Navegación mejorada */
    .calendario-navegacion {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .calendario-navegacion .btn {
        font-size: 0.8125rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #374151;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .calendario-navegacion .btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        color: #111827;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .calendario-navegacion .btn:active {
        transform: translateY(0);
    }

    .calendario-navegacion h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
        letter-spacing: -0.01em;
    }

    /* Grid del calendario mejorado */
    .calendario-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .calendario-header {
        display: contents;
    }

    .calendario-dia-header {
        background: #f9fafb;
        padding: 0.875rem 0.5rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e5e7eb;
    }

    .calendario-dias {
        display: contents;
    }

    .calendario-dia {
        background: #ffffff;
        min-height: 110px;
        padding: 0.5rem;
        display: flex;
        flex-direction: column;
        position: relative;
        border-right: 1px solid #f3f4f6;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.15s ease;
    }

    .calendario-dia:hover {
        background: #fafbfc;
    }

    .calendario-dia:last-child,
    .calendario-dia:nth-child(7n) {
        border-right: none;
    }

    .calendario-dia-vacio {
        background: transparent;
        border: none;
        min-height: 110px;
        padding: 0;
        cursor: default;
    }

    .calendario-dia-vacio:hover {
        background: transparent;
    }

    .calendario-dia.today {
        background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
        border-left: 3px solid #3b82f6;
    }

    .calendario-dia.today:hover {
        background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);
    }

    .calendario-dia.today .calendario-dia-numero {
        color: #2563eb;
        font-weight: 700;
    }

    .calendario-dia-numero {
        font-size: 0.8125rem;
        font-weight: 600;
        margin-bottom: 0.375rem;
        position: relative;
        color: #374151;
        line-height: 1.2;
    }

    .calendario-hoy-badge {
        display: inline-block;
        background: #3b82f6;
        color: #ffffff;
        font-size: 0.625rem;
        padding: 0.125rem 0.375rem;
        border-radius: 4px;
        margin-left: 0.375rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        box-shadow: 0 1px 2px rgba(59, 130, 246, 0.3);
    }

    .calendario-tareas {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        min-height: 0;
    }

    .calendario-tarea {
        font-size: 0.6875rem;
        padding: 0.375rem 0.5rem;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-weight: 500;
        line-height: 1.3;
        position: relative;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .calendario-tarea:hover {
        transform: translateX(3px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        z-index: 10;
    }

    .calendario-tarea-nombre {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .calendario-tarea[title]:hover::after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: #ffffff;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 1000;
        margin-bottom: 0.25rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        pointer-events: none;
    }

    .calendario-tarea[title]:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 4px solid transparent;
        border-top-color: #1f2937;
        margin-bottom: -4px;
        z-index: 1000;
        pointer-events: none;
    }

    .calendario-tarea-mas {
        font-size: 0.625rem;
        color: #6b7280;
        padding: 0.25rem 0.5rem;
        font-style: normal;
        font-weight: 500;
        background: #f3f4f6;
        border-radius: 4px;
        cursor: default;
    }

    /* Colores de tareas según estado */
    .tarea-vencida {
        background: #fee2e2;
        color: #991b1b;
        border-left: 3px solid #dc2626;
        box-shadow: 0 1px 3px rgba(220, 38, 38, 0.2);
    }

    .tarea-vencida:hover {
        background: #fecaca;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }

    .tarea-completada {
        background: #d1fae5;
        color: #065f46;
        border-left: 3px solid #10b981;
        box-shadow: 0 1px 3px rgba(16, 185, 129, 0.2);
    }

    .tarea-completada:hover {
        background: #a7f3d0;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
    }

    .tarea-en-progreso,
    .tarea-pendiente {
        background: #212B36;
        color: #ffffff;
        border-left: 3px solid #212B36;
        box-shadow: 0 1px 3px rgba(33, 43, 54, 0.3);
    }

    .tarea-en-progreso:hover,
    .tarea-pendiente:hover {
        background: #2d3748;
        box-shadow: 0 2px 6px rgba(33, 43, 54, 0.4);
    }

    .tarea-prioridad-alta {
        background: #212B36;
        color: #ffffff;
        border-left: 3px solid #212B36;
        box-shadow: 0 1px 3px rgba(33, 43, 54, 0.3);
    }

    .tarea-prioridad-alta:hover {
        background: #2d3748;
        box-shadow: 0 2px 6px rgba(33, 43, 54, 0.4);
    }

    .tarea-prioridad-media {
        background: #212B36;
        color: #ffffff;
        border-left: 3px solid #212B36;
        box-shadow: 0 1px 3px rgba(33, 43, 54, 0.3);
    }

    .tarea-prioridad-media:hover {
        background: #2d3748;
        box-shadow: 0 2px 6px rgba(33, 43, 54, 0.4);
    }

    .tarea-prioridad-baja {
        background: #212B36;
        color: #ffffff;
        border-left: 3px solid #212B36;
        box-shadow: 0 1px 3px rgba(33, 43, 54, 0.3);
    }

    .tarea-prioridad-baja:hover {
        background: #2d3748;
        box-shadow: 0 2px 6px rgba(33, 43, 54, 0.4);
    }

    @media (max-width: 768px) {
        .calendario-container {
            padding: 1rem;
            border-radius: 8px;
        }

        .calendario-navegacion {
            padding: 0.5rem 0;
            margin-bottom: 1rem;
        }

        .calendario-navegacion .btn {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .calendario-navegacion h3 {
            font-size: 1.125rem;
        }

        .calendario-dia {
            min-height: 85px;
            padding: 0.375rem;
        }

        .calendario-tarea {
            font-size: 0.625rem;
            padding: 0.3rem 0.4rem;
        }

        .calendario-dia-numero {
            font-size: 0.75rem;
            margin-bottom: 0.3rem;
        }

        .calendario-dia-header {
            padding: 0.625rem 0.375rem;
            font-size: 0.6875rem;
        }

        .calendario-tarea-mas {
            font-size: 0.5625rem;
            padding: 0.2rem 0.4rem;
        }

        .calendario-hoy-badge {
            font-size: 0.5625rem;
            padding: 0.1rem 0.3rem;
        }
    }

    /* ============================================
       ESTILOS DE PESTAÑAS
       ============================================ */
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        color: #000000;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .nav-tabs .nav-link:hover {
        color: #000000;
        border-bottom-color: #dee2e6;
    }

    .nav-tabs .nav-link.active {
        color: #000000;
        font-weight: 600;
        border-bottom-color: #0D6EFD;
        background-color: transparent;
    }

    /* ============================================
       DISEÑO MOBILE-FIRST - COLABORADORES (ESTILO GRID)
       ============================================ */
    .colaboradores-container {
        background: transparent;
        padding: 0;
    }

    .colaboradores-header {
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .colaboradores-header h3 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #212529;
        margin: 0;
    }

    .colaboradores-count {
        font-size: 0.75rem;
        font-weight: 400;
        color: #6c757d;
        margin-left: 0.375rem;
    }

    .colaboradores-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .colaborador-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 0.75rem 0.5rem;
        transition: opacity 0.2s ease;
    }

    .colaborador-item:hover {
        opacity: 0.8;
    }

    .colaborador-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        color: #ffffff;
        background: var(--colaborador-color, #0D6EFD);
        margin-bottom: 0.5rem;
        flex-shrink: 0;
    }

    .colaborador-info {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .colaborador-name {
        font-size: 0.75rem;
        font-weight: 500;
        color: #212529;
        margin: 0;
        line-height: 1.3;
        word-wrap: break-word;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .colaborador-email {
        font-size: 0.6875rem;
        color: #6c757d;
        margin: 0;
        word-break: break-word;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .colaborador-role {
        font-size: 0.625rem;
        font-weight: 500;
        color: #6c757d;
        margin: 0;
        text-transform: capitalize;
    }

    .colaborador-empty-state {
        text-align: center;
        padding: 2rem 1rem;
        background: #ffffff;
        border-radius: 6px;
        border: 1px dashed #dee2e6;
    }

    .colaborador-empty-state-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 0.75rem;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 1.25rem;
    }

    .colaborador-empty-state h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #495057;
        margin: 0 0 0.25rem 0;
    }

    .colaborador-empty-state p {
        font-size: 0.75rem;
        color: #6c757d;
        margin: 0;
    }

    /* Tablet - 768px and up */
    @media (min-width: 768px) {
        .colaboradores-header {
            margin-bottom: 1.25rem;
        }

        .colaboradores-header h3 {
            font-size: 1rem;
        }

        .colaboradores-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }

        .colaborador-item {
            padding: 1rem 0.75rem;
        }

        .colaborador-avatar {
            width: 56px;
            height: 56px;
            font-size: 1rem;
            margin-bottom: 0.625rem;
        }

        .colaborador-name {
            font-size: 0.8125rem;
        }

        .colaborador-email {
            font-size: 0.75rem;
        }

        .colaborador-role {
            font-size: 0.6875rem;
        }
    }

    /* Desktop - 992px and up */
    @media (min-width: 992px) {
        .colaboradores-header h3 {
            font-size: 1.125rem;
        }

        .colaboradores-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .colaborador-item {
            padding: 1.25rem 1rem;
        }

        .colaborador-avatar {
            width: 64px;
            height: 64px;
            font-size: 1.125rem;
            margin-bottom: 0.75rem;
        }

        .colaborador-name {
            font-size: 0.875rem;
        }

        .colaborador-email {
            font-size: 0.8125rem;
        }

        .colaborador-role {
            font-size: 0.75rem;
        }
    }
</style>

@php
    $auth_user = \App\Models\User::with('roles')->find(session('user_id'));
@endphp

<div class="container">
    {{-- Header --}}
    <div class="actividades-header">
        <div class="header-title-section">
            <h1 class="actividades-title">
                Fases del Proyecto
                <span class="actividades-count">{{ $actividades->count() }}</span>
            </h1>
            <p class="actividades-subtitle">Gestione el progreso y los detalles de las tareas.</p>
        </div>
        <div class="header-actions">
            @permiso('crear actividades')
            <button type="button" class="btn-action btn-add-activity" data-bs-toggle="modal" data-bs-target="#modalCrearActividad">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">Agregar Fase</span>
            </button>
            @endpermiso
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Pestañas --}}
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tabActivo === 'fases' ? 'active' : '' }}" 
               href="{{ route('actividades.index', $proyectoId) }}" 
               role="tab">
                <i class="bi bi-file-text me-1"></i> Fases
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tabActivo === 'calendario' ? 'active' : '' }}" 
               href="{{ route('actividades.index', $proyectoId) }}?mes={{ $mes ?? \Carbon\Carbon::now('America/Bogota')->month }}&ano={{ $ano ?? \Carbon\Carbon::now('America/Bogota')->year }}" 
               role="tab">
                <i class="bi bi-calendar me-1"></i> Calendario
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tabActivo === 'proyectos' ? 'active' : '' }}" 
               href="{{ route('proyectos.index') }}" 
               role="tab">
                <i class="bi bi-folder me-1"></i> Proyectos
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tabActivo === 'usuarios' ? 'active' : '' }}" 
               href="{{ route('actividades.index', $proyectoId) }}?tab=usuarios" 
               role="tab">
                <i class="bi bi-people me-1"></i> Colaboradores
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tabActivo === 'analisis' ? 'active' : '' }}" 
               href="{{ route('proyectos.metricas', $proyectoId) }}" 
               role="tab">
                <i class="bi bi-graph-up me-1"></i> Análisis
            </a>
        </li>
    </ul>

    {{-- Contenido de las pestañas --}}
    <div class="tab-content">
        {{-- Pestaña Fases --}}
        <div class="tab-pane fade {{ $tabActivo === 'fases' ? 'show active' : '' }}" id="fases" role="tabpanel">
            {{-- Lista de Fases --}}
    @forelse($actividades as $actividad)
        @php
            $auth_user = \App\Models\User::with('roles')->find(session('user_id'));
            $puedeGestionar = $proyecto->puedeGestionarActividadesYTareas($auth_user);
        @endphp
        <div class="fase-card" 
             data-actividad-id="{{ $actividad->id }}"
             data-actividad-nombre="{{ $actividad->nombre }}"
             data-actividad-descripcion="{{ $actividad->descripcion ?? '' }}"
             data-actividad-fecha-inicio="{{ $actividad->fecha_inicio ? $actividad->fecha_inicio->format('Y-m-d') : '' }}"
             data-actividad-fecha-fin="{{ $actividad->fecha_fin ? $actividad->fecha_fin->format('Y-m-d') : '' }}"
             data-actividad-hora-fin="{{ $actividad->fecha_fin ? $actividad->fecha_fin->format('H:i') : '' }}"
             data-actividad-estado="{{ $actividad->estado }}">
            <div class="fase-header">
                <div class="d-flex align-items-center flex-grow-1" onclick="toggleFase({{ $actividad->id }})" style="cursor: pointer;">
                    <div class="fase-icon">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div class="fase-info">
                        <div class="fase-title">{{ $actividad->nombre }}</div>
                        <div class="fase-meta">
                            Creado el {{ $actividad->created_at->format('d M Y') }} • 
                            {{ $actividad->tareas_pendientes ?? 0 }} tareas pendientes
                        </div>
                        @php
                            $totalTareas = $actividad->tareas_count ?? 0;
                            $tareasCompletadas = $totalTareas > 0 ? $totalTareas - ($actividad->tareas_pendientes ?? 0) : 0;
                            $porcentaje = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100, 1) : 0;
                            $claseProgreso = $porcentaje == 100 ? 'completed' : ($porcentaje >= 50 ? '' : 'warning');
                        @endphp
                        @if($totalTareas > 0)
                        <div class="fase-progress">
                            <div class="fase-progress-info">
                                <span>{{ $tareasCompletadas }} de {{ $totalTareas }} tareas completadas</span>
                                <span class="fase-progress-percentage">{{ $porcentaje }}%</span>
                            </div>
                            <div class="fase-progress-bar-container">
                                <div class="fase-progress-bar {{ $claseProgreso }}" style="width: {{ $porcentaje }}%;"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                    @if($puedeGestionar)
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary" 
                                onclick="editarActividad({{ $actividad->id }})"
                                title="Editar Fase">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('actividades.destroy', $actividad->id) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirmarEliminarActividad(event, '{{ $actividad->nombre }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm btn-outline-danger" 
                                    title="Eliminar Fase">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                    <i class="bi bi-chevron-down fase-toggle" id="toggle-{{ $actividad->id }}" onclick="toggleFase({{ $actividad->id }})" style="cursor: pointer;"></i>
                </div>
            </div>

            <div class="tareas-container" id="tareas-{{ $actividad->id }}">
                {{-- Contenedor para cargar tareas paginadas --}}
                <div id="tareas-content-{{ $actividad->id }}" class="tareas-content">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>

                {{-- Botón/Enlace para agregar tarea --}}
                @permiso('crear tarea')
                <div class="py-2 border-top">
                    <button type="button" class="btn btn-link text-primary text-decoration-none p-0 fw-medium" onclick="mostrarFormTarea({{ $actividad->id }})">
                        <i class="bi bi-plus-lg me-1"></i> Agregar Tarea a {{ $actividad->nombre }}
                    </button>
                </div>

                {{-- Formulario para crear tarea (oculto por defecto) --}}
                <div class="form-crear-tarea py-2 border-top" id="form-tarea-{{ $actividad->id }}" style="display: none;">
                    <form action="{{ route('tareas.store') }}" method="POST" class="form-crear-tarea-submit" data-actividad-id="{{ $actividad->id }}">
                        @csrf
                        <input type="hidden" name="actividad_id" value="{{ $actividad->id }}">
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small">Nombre de la tarea</label>
                                <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Nombre de la tarea" required>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label small">Prioridad</label>
                                <select name="prioridad" class="form-select form-select-sm">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label small">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label small">Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="form-control form-control-sm">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label small">Hora Fin</label>
                                <input type="time" name="hora_fin" class="form-control form-control-sm">
                            </div>

                            @if($colaboradores && $colaboradores->count() > 0)
                            <div class="col-12">
                                <label class="form-label small">Asignar a colaboradores</label>
                                <select name="usuarios[]" class="form-select form-select-sm" multiple size="3">
                                    @foreach($colaboradores as $colaborador)
                                        <option value="{{ $colaborador->id }}">{{ $colaborador->nombre }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Mantenga presionado Ctrl/Cmd para seleccionar múltiples</small>
                            </div>
                            @endif

                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-check-lg"></i> Crear Tarea
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="ocultarFormTarea({{ $actividad->id }})">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @endpermiso
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No hay fases registradas para este proyecto.
        </div>
    @endforelse
        </div>

        {{-- Pestaña Calendario --}}
        <div class="tab-pane fade {{ $tabActivo === 'calendario' ? 'show active' : '' }}" id="calendario" role="tabpanel">
            <div class="calendario-container">
                {{-- Navegación del calendario --}}
                <div class="calendario-navegacion">
                    <button type="button" class="btn" onclick="cambiarMes(-1)">
                        <i class="bi bi-chevron-left"></i> Anterior
                    </button>
                    <h3>{{ $nombreMes ?? 'Enero' }} {{ $ano ?? date('Y') }}</h3>
                    <button type="button" class="btn" onclick="cambiarMes(1)">
                        Siguiente <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                {{-- Grid del calendario --}}
                <div class="calendario-grid">
                    {{-- Headers de días de la semana --}}
                    <div class="calendario-header">
                        @foreach($diasSemana ?? ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $diaSemana)
                            <div class="calendario-dia-header">{{ $diaSemana }}</div>
                        @endforeach
                    </div>

                    {{-- Días del mes --}}
                    <div class="calendario-dias">
                        @foreach($diasMes ?? [] as $diaInfo)
                            @if($diaInfo['vacio'] ?? false)
                                <div class="calendario-dia calendario-dia-vacio"></div>
                            @else
                                <div class="calendario-dia {{ ($diaInfo['es_hoy'] ?? false) ? 'today' : '' }}">
                                    <div class="calendario-dia-numero">
                                        {{ $diaInfo['dia'] ?? '' }}
                                        @if($diaInfo['es_hoy'] ?? false)
                                            <span class="calendario-hoy-badge">HOY</span>
                                        @endif
                                    </div>
                                    <div class="calendario-tareas">
                                        @php
                                            $tareasDia = $diaInfo['tareas'] ?? collect();
                                            $tareasMostrar = $tareasDia->take(3);
                                            $tareasRestantes = $tareasDia->count() - 3;
                                        @endphp
                                        @foreach($tareasMostrar as $tarea)
                                            @php
                                                $esVencida = \Carbon\Carbon::parse($tarea->fecha_fin)->isPast() && $tarea->estado !== 'completado';
                                                $colorClase = '';
                                                if ($esVencida) {
                                                    $colorClase = 'tarea-vencida';
                                                } elseif ($tarea->estado === 'completado') {
                                                    $colorClase = 'tarea-completada';
                                                } elseif ($tarea->estado === 'en_progreso') {
                                                    $colorClase = 'tarea-en-progreso';
                                                } elseif ($tarea->estado === 'pendiente') {
                                                    $colorClase = 'tarea-pendiente';
                                                } else {
                                                    $colorClase = 'tarea-pendiente';
                                                }
                                            @endphp
                                            <div class="calendario-tarea {{ $colorClase }}" 
                                                 onclick="abrirPanelEvidenciasTarea({{ $tarea->id }})"
                                                 title="{{ $tarea->nombre }}">
                                                <span class="calendario-tarea-nombre">{{ Str::limit($tarea->nombre, 20) }}</span>
                                            </div>
                                        @endforeach
                                        @if($tareasRestantes > 0)
                                            <div class="calendario-tarea-mas">
                                                + {{ $tareasRestantes }} más
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Pestaña Colaboradores --}}
        <div class="tab-pane fade {{ $tabActivo === 'usuarios' ? 'show active' : '' }}" id="usuarios" role="tabpanel">
            <div class="colaboradores-container">
                <div class="colaboradores-header">
                    <h3>
                        Colaboradores
                        <span class="colaboradores-count">({{ $proyecto->colaboradores->count() }})</span>
                    </h3>
                </div>

                @if($proyecto->colaboradores->count() > 0)
                    <div class="colaboradores-grid">
                        @foreach($proyecto->colaboradores as $colaborador)
                            @php
                                $colorColaborador = $colaborador->color ?? '#0D6EFD';
                                $iniciales = strtoupper(substr($colaborador->nombre, 0, 2));
                                // Dividir nombre en dos líneas si tiene espacio
                                $nombrePartes = explode(' ', $colaborador->nombre, 2);
                                $primerNombre = $nombrePartes[0];
                                $segundoNombre = isset($nombrePartes[1]) ? $nombrePartes[1] : '';
                            @endphp
                            <div class="colaborador-item" style="--colaborador-color: {{ $colorColaborador }};">
                                <div class="colaborador-avatar" style="background: {{ $colorColaborador }};">
                                    {{ $iniciales }}
                                </div>
                                <div class="colaborador-info">
                                    <div class="colaborador-name">
                                        @if($segundoNombre)
                                            {{ $primerNombre }}<br>{{ $segundoNombre }}
                                        @else
                                            {{ $colaborador->nombre }}
                                        @endif
                                    </div>
                                    <div class="colaborador-email">{{ $colaborador->email }}</div>
                                    <div class="colaborador-role">{{ ucfirst($colaborador->pivot->rol_proyecto ?? 'Colaborador') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="colaborador-empty-state">
                        <div class="colaborador-empty-state-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4>No hay colaboradores</h4>
                        <p>Este proyecto aún no tiene colaboradores asignados.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Panel Lateral de Evidencias de Tarea --}}
<div class="panel-evidencias-tarea-overlay" id="panelEvidenciasTareaOverlay" onclick="cerrarPanelEvidenciasTarea()"></div>
<div class="panel-evidencias-tarea" id="panelEvidenciasTarea" onclick="event.stopPropagation()">
    <div class="panel-evidencias-tarea-header">
        <h4 class="mb-0">Evidencias de la Tarea</h4>
        <button type="button" class="btn-close" onclick="cerrarPanelEvidenciasTarea()"></button>
    </div>
    <div class="panel-evidencias-tarea-body" id="panelEvidenciasTareaBody">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>
</div>

{{-- Modal para crear fase --}}
@permiso('crear actividades')
<div class="modal fade" id="modalCrearActividad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nueva Fase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('actividades.store') }}" method="POST">
                @csrf
                <input type="hidden" name="proyecto_id" value="{{ $proyectoId }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Fin</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora de Fin</label>
                            <input type="time" name="hora_fin" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Fase</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpermiso

{{-- Modal para editar fase --}}
<div class="modal fade" id="modalEditarActividad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Fase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarActividad" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="edit-actividad-nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit-actividad-descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="date" name="fecha_inicio" id="edit-actividad-fecha-inicio" class="form-control">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Fin</label>
                            <input type="date" name="fecha_fin" id="edit-actividad-fecha-fin" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora de Fin</label>
                            <input type="time" name="hora_fin" id="edit-actividad-hora-fin" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" id="edit-actividad-estado" class="form-select">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para editar tarea --}}
<div class="modal fade" id="modalEditarTarea" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarTarea" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="edit-tarea-nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit-tarea-descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Prioridad</label>
                            <select name="prioridad" id="edit-tarea-prioridad" class="form-select">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select name="estado" id="edit-tarea-estado" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completado">Completado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="edit-tarea-fecha-inicio" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="edit-tarea-fecha-fin" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora Fin</label>
                            <input type="time" name="hora_fin" id="edit-tarea-hora-fin" class="form-control">
                        </div>
                    </div>
                    @if(isset($colaboradores) && $colaboradores && $colaboradores->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Asignar a colaboradores</label>
                        <select name="usuarios[]" id="edit-tarea-usuarios" class="form-select" multiple size="4">
                            @foreach($colaboradores as $colaborador)
                                <option value="{{ $colaborador->id }}">{{ $colaborador->nombre }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mantenga presionado Ctrl/Cmd para seleccionar múltiples</small>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para confirmar eliminación --}}
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="mensaje-eliminar">¿Está seguro de eliminar este elemento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminarConfirmado" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal para colaboradores --}}
<div class="modal fade" id="modalColaboradores" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Colaboradores del Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($proyecto->colaboradores->count() > 0)
                    <ul class="list-group">
                        @foreach($proyecto->colaboradores as $colaborador)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $colaborador->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $colaborador->email }}</small>
                                </div>
                                <span class="badge bg-primary">{{ ucfirst($colaborador->pivot->rol_proyecto) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No hay colaboradores asignados a este proyecto.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFase(actividadId) {
    const container = document.getElementById(`tareas-${actividadId}`);
    const toggle = document.getElementById(`toggle-${actividadId}`);
    const header = toggle.closest('.fase-header');
    const faseCard = toggle.closest('.fase-card');
    const tareasContent = document.getElementById(`tareas-content-${actividadId}`);
    
    // Obtener el ID del proyecto para la clave de localStorage
    const proyectoId = {{ $proyecto->id }};
    const storageKey = `actividades_expandidas_${proyectoId}`;
    
    if (container.classList.contains('show')) {
        container.classList.remove('show');
        toggle.classList.remove('expanded');
        header.classList.remove('expanded');
        if (faseCard) faseCard.classList.remove('expanded');
        
        // Guardar estado: remover de la lista de expandidas
        guardarEstadoActividades(proyectoId, actividadId, false);
    } else {
        container.classList.add('show');
        toggle.classList.add('expanded');
        header.classList.add('expanded');
        if (faseCard) faseCard.classList.add('expanded');
        
        // Guardar estado: agregar a la lista de expandidas
        guardarEstadoActividades(proyectoId, actividadId, true);
        
        // Cargar tareas paginadas si no se han cargado aún
        if (tareasContent && !tareasContent.dataset.loaded) {
            cargarTareas(actividadId, 1);
        }
    }
}

// Función para guardar el estado de las actividades expandidas
function guardarEstadoActividades(proyectoId, actividadId, expandida) {
    const storageKey = `actividades_expandidas_${proyectoId}`;
    let actividadesExpandidas = JSON.parse(localStorage.getItem(storageKey) || '[]');
    
    if (expandida) {
        // Agregar si no está ya en la lista
        if (!actividadesExpandidas.includes(actividadId)) {
            actividadesExpandidas.push(actividadId);
        }
    } else {
        // Remover de la lista
        actividadesExpandidas = actividadesExpandidas.filter(id => id !== actividadId);
    }
    
    localStorage.setItem(storageKey, JSON.stringify(actividadesExpandidas));
}

// Función para restaurar el estado de las actividades expandidas
function restaurarEstadoActividades() {
    const proyectoId = {{ $proyecto->id }};
    const storageKey = `actividades_expandidas_${proyectoId}`;
    const actividadesExpandidas = JSON.parse(localStorage.getItem(storageKey) || '[]');
    
    actividadesExpandidas.forEach(actividadId => {
        const container = document.getElementById(`tareas-${actividadId}`);
        const toggle = document.getElementById(`toggle-${actividadId}`);
        const header = toggle ? toggle.closest('.fase-header') : null;
        const faseCard = toggle ? toggle.closest('.fase-card') : null;
        const tareasContent = document.getElementById(`tareas-content-${actividadId}`);
        
        if (container && toggle) {
            container.classList.add('show');
            toggle.classList.add('expanded');
            if (header) header.classList.add('expanded');
            if (faseCard) faseCard.classList.add('expanded');
            
            // Cargar tareas si no se han cargado aún
            if (tareasContent && !tareasContent.dataset.loaded) {
                cargarTareas(actividadId, 1);
            }
        }
    });
}

function cargarTareas(actividadId, page = 1) {
    const tareasContent = document.getElementById(`tareas-content-${actividadId}`);
    if (!tareasContent) return;
    
    tareasContent.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    
    const url = `{{ url('actividades') }}/${actividadId}/tareas?page=${page}&per_page=10`;
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        tareasContent.innerHTML = data.html;
        tareasContent.dataset.loaded = 'true';
        
        // Agregar event listeners a los enlaces de paginación
        const paginationLinks = tareasContent.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page') || 1;
                cargarTareas(actividadId, page);
            });
        });
    })
    .catch(error => {
        console.error('Error al cargar tareas:', error);
        tareasContent.innerHTML = '<div class="alert alert-danger">Error al cargar las tareas. Por favor, recargue la página.</div>';
    });
}

function toggleTareaCompletada(tareaId, completada) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch(`/tareas/${tareaId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar la página para reflejar los cambios
            location.reload();
        } else {
            mostrarError('Error al actualizar la tarea');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error al actualizar la tarea');
    });
}

function toggleDropdownEstado(tareaId) {
    // Cerrar otros dropdowns
    document.querySelectorAll('.dropdown-menu-estado').forEach(dropdown => {
        if (dropdown.id !== `dropdown-estado-${tareaId}`) {
            dropdown.style.display = 'none';
        }
    });
    
    // Toggle el dropdown actual
    const dropdown = document.getElementById(`dropdown-estado-${tareaId}`);
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
    }
}

function cambiarEstado(tareaId, nuevoEstado) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('estado', nuevoEstado);
    
    fetch(`/tareas/${tareaId}/toggle-estado`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar dropdown
            const dropdown = document.getElementById(`dropdown-estado-${tareaId}`);
            if (dropdown) {
                dropdown.style.display = 'none';
            }
            // Recargar la página para reflejar los cambios
            location.reload();
        } else {
            mostrarError(data.message || 'Error al actualizar el estado de la tarea');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error al actualizar el estado de la tarea');
    });
}

function mostrarDatePicker(tareaId) {
    // Ocultar otros date pickers
    document.querySelectorAll('.date-picker-inline').forEach(picker => {
        if (picker.id !== `date-picker-${tareaId}`) {
            picker.style.display = 'none';
        }
    });
    
    // Mostrar el date picker actual
    const picker = document.getElementById(`date-picker-${tareaId}`);
    if (picker) {
        picker.style.display = 'block';
    }
}

function ocultarDatePicker(tareaId) {
    const picker = document.getElementById(`date-picker-${tareaId}`);
    if (picker) {
        picker.style.display = 'none';
    }
}

function guardarFecha(tareaId) {
    const fechaFin = document.getElementById(`fecha-fin-${tareaId}`).value;
    const horaFin = document.getElementById(`hora-fin-${tareaId}`).value;
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('fecha_fin', fechaFin);
    if (horaFin) {
        formData.append('hora_fin', horaFin);
    }
    
    fetch(`/tareas/${tareaId}/update-fecha`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ocultar date picker
            ocultarDatePicker(tareaId);
            // Recargar la página para reflejar los cambios
            location.reload();
        } else {
            mostrarError(data.message || 'Error al actualizar la fecha');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error al actualizar la fecha');
    });
}

// Cerrar dropdowns al hacer clic fuera
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown-estado-inline')) {
        document.querySelectorAll('.dropdown-menu-estado').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
    if (!event.target.closest('.fecha-clickable') && !event.target.closest('.date-picker-inline')) {
        document.querySelectorAll('.date-picker-inline').forEach(picker => {
            picker.style.display = 'none';
        });
    }
});

function toggleDropdownEstado(tareaId) {
    // Cerrar otros dropdowns
    document.querySelectorAll('.dropdown-menu-estado').forEach(dropdown => {
        if (dropdown.id !== `dropdown-estado-${tareaId}`) {
            dropdown.style.display = 'none';
        }
    });
    
    // Toggle el dropdown actual
    const dropdown = document.getElementById(`dropdown-estado-${tareaId}`);
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
    }
}

function cambiarEstado(tareaId, nuevoEstado) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('estado', nuevoEstado);
    
    fetch(`/tareas/${tareaId}/toggle-estado`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar dropdown
            const dropdown = document.getElementById(`dropdown-estado-${tareaId}`);
            if (dropdown) {
                dropdown.style.display = 'none';
            }
            // Recargar la página para reflejar los cambios
            location.reload();
        } else {
            mostrarError(data.message || 'Error al actualizar el estado de la tarea');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error al actualizar el estado de la tarea');
    });
}

function mostrarDatePicker(tareaId) {
    // Ocultar otros date pickers
    document.querySelectorAll('.date-picker-inline').forEach(picker => {
        if (picker.id !== `date-picker-${tareaId}`) {
            picker.style.display = 'none';
        }
    });
    
    // Mostrar el date picker actual
    const picker = document.getElementById(`date-picker-${tareaId}`);
    if (picker) {
        picker.style.display = 'block';
    }
}

function ocultarDatePicker(tareaId) {
    const picker = document.getElementById(`date-picker-${tareaId}`);
    if (picker) {
        picker.style.display = 'none';
    }
}

function guardarFecha(tareaId) {
    const fechaFin = document.getElementById(`fecha-fin-${tareaId}`).value;
    const horaFin = document.getElementById(`hora-fin-${tareaId}`).value;
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('fecha_fin', fechaFin);
    if (horaFin) {
        formData.append('hora_fin', horaFin);
    }
    
    fetch(`/tareas/${tareaId}/update-fecha`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ocultar date picker
            ocultarDatePicker(tareaId);
            // Recargar la página para reflejar los cambios
            location.reload();
        } else {
            mostrarError(data.message || 'Error al actualizar la fecha');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error al actualizar la fecha');
    });
}

// Cerrar dropdowns al hacer clic fuera
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown-estado-inline')) {
        document.querySelectorAll('.dropdown-menu-estado').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
    if (!event.target.closest('.fecha-clickable') && !event.target.closest('.date-picker-inline')) {
        document.querySelectorAll('.date-picker-inline').forEach(picker => {
            picker.style.display = 'none';
        });
    }
});

function mostrarFormTarea(actividadId) {
    const form = document.getElementById(`form-tarea-${actividadId}`);
    if (form) {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function ocultarFormTarea(actividadId) {
    const form = document.getElementById(`form-tarea-${actividadId}`);
    if (form) {
        form.style.display = 'none';
    }
}

// Inicializar cuando la página carga
document.addEventListener('DOMContentLoaded', function() {
    // Restaurar estado de actividades expandidas
    restaurarEstadoActividades();
    
    // Guardar estado antes de enviar formularios de crear tarea
    document.querySelectorAll('.form-crear-tarea-submit').forEach(form => {
        form.addEventListener('submit', function(e) {
            const actividadId = parseInt(this.dataset.actividadId);
            const proyectoId = {{ $proyecto->id }};
            
            // Asegurar que la actividad esté marcada como expandida antes de enviar
            guardarEstadoActividades(proyectoId, actividadId, true);
        });
    });
});

function toggleAllTasks(actividadId, checked) {
    const checkboxes = document.querySelectorAll(`#tareas-${actividadId} .tarea-checkbox`);
    checkboxes.forEach(checkbox => {
        if (checkbox.checked !== checked) {
            checkbox.checked = checked;
            const tareaId = checkbox.getAttribute('data-tarea-id');
            if (tareaId) {
                toggleTareaCompletada(parseInt(tareaId), checked);
            }
        }
    });
}

function editarActividad(actividadId) {
    // Buscar los datos de la actividad en el DOM
    const actividadCard = document.querySelector(`[data-actividad-id="${actividadId}"]`);
    if (!actividadCard) {
        mostrarError('No se encontraron los datos de la actividad');
        return;
    }

    // Verificar que los elementos existan antes de usarlos
    const nombreInput = document.getElementById('edit-actividad-nombre');
    const descripcionInput = document.getElementById('edit-actividad-descripcion');
    const fechaInicioInput = document.getElementById('edit-actividad-fecha-inicio');
    const fechaFinInput = document.getElementById('edit-actividad-fecha-fin');
    const horaFinInput = document.getElementById('edit-actividad-hora-fin');
    const estadoSelect = document.getElementById('edit-actividad-estado');
    const formActividad = document.getElementById('formEditarActividad');
    
    if (!nombreInput || !descripcionInput || !fechaInicioInput || !fechaFinInput || !horaFinInput || !estadoSelect || !formActividad) {
        mostrarError('No se encontraron los elementos del formulario de edición');
        return;
    }

    // Cargar datos en el formulario
    nombreInput.value = actividadCard.dataset.actividadNombre || '';
    descripcionInput.value = actividadCard.dataset.actividadDescripcion || '';
    fechaInicioInput.value = actividadCard.dataset.actividadFechaInicio || '';
    fechaFinInput.value = actividadCard.dataset.actividadFechaFin || '';
    horaFinInput.value = actividadCard.dataset.actividadHoraFin || '';
    estadoSelect.value = actividadCard.dataset.actividadEstado || 'pendiente';
    
    // Configurar acción del formulario
    formActividad.action = `{{ url('actividades') }}/${actividadId}`;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditarActividad'));
    modal.show();
}

function editarTarea(tareaId, actividadId) {
    // Obtener el botón con los datos
    const botonTarea = document.querySelector(`button[data-tarea-id="${tareaId}"]`);
    if (!botonTarea) {
        mostrarError('No se encontraron los datos de la tarea');
        return;
    }

    // Verificar que los elementos existan antes de usarlos
    const nombreInput = document.getElementById('edit-tarea-nombre');
    const descripcionInput = document.getElementById('edit-tarea-descripcion');
    const fechaInicioInput = document.getElementById('edit-tarea-fecha-inicio');
    const fechaFinInput = document.getElementById('edit-tarea-fecha-fin');
    const horaFinInput = document.getElementById('edit-tarea-hora-fin');
    const prioridadSelect = document.getElementById('edit-tarea-prioridad');
    const estadoSelect = document.getElementById('edit-tarea-estado');
    const formTarea = document.getElementById('formEditarTarea');
    
    if (!nombreInput || !descripcionInput || !fechaInicioInput || !fechaFinInput || !horaFinInput || !prioridadSelect || !estadoSelect || !formTarea) {
        mostrarError('No se encontraron los elementos del formulario de edición');
        return;
    }

    // Cargar datos en el formulario
    nombreInput.value = botonTarea.dataset.tareaNombre || '';
    descripcionInput.value = botonTarea.dataset.tareaDescripcion || '';
    fechaInicioInput.value = botonTarea.dataset.tareaFechaInicio || '';
    fechaFinInput.value = botonTarea.dataset.tareaFechaFin || '';
    horaFinInput.value = botonTarea.dataset.tareaHoraFin || '';
    prioridadSelect.value = botonTarea.dataset.tareaPrioridad || 'media';
    estadoSelect.value = botonTarea.dataset.tareaEstado || 'pendiente';
    
    // Cargar usuarios asignados
    const usuariosIds = JSON.parse(botonTarea.dataset.tareaUsuarios || '[]');
    const selectUsuarios = document.getElementById('edit-tarea-usuarios');
    if (selectUsuarios) {
        Array.from(selectUsuarios.options).forEach(option => {
            option.selected = usuariosIds.includes(parseInt(option.value));
        });
    }
    
    // Configurar acción del formulario
    formTarea.action = `{{ url('tareas') }}/${tareaId}`;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditarTarea'));
    modal.show();
}

function confirmarEliminarActividad(event, nombreActividad) {
    event.preventDefault();
    const form = event.target.closest('form');
    
    const mensajeEliminar = document.getElementById('mensaje-eliminar');
    const formConfirmado = document.getElementById('formEliminarConfirmado');
    const modalEliminar = document.getElementById('modalConfirmarEliminar');
    
    if (!mensajeEliminar || !formConfirmado || !modalEliminar) {
        mostrarError('No se encontraron los elementos del modal de confirmación');
        return;
    }
    
    mensajeEliminar.textContent = `¿Está seguro de eliminar la fase "${nombreActividad}"?`;
    formConfirmado.action = form.action;
    
    const modal = new bootstrap.Modal(modalEliminar);
    
    // Remover listeners anteriores y agregar nuevo
    const nuevoForm = formConfirmado.cloneNode(true);
    formConfirmado.parentNode.replaceChild(nuevoForm, formConfirmado);
    
    nuevoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        modal.hide();
        form.submit();
    });
    
    modal.show();
}

function confirmarEliminarTarea(event, nombreTarea) {
    event.preventDefault();
    const form = event.target.closest('form');
    
    const mensajeEliminar = document.getElementById('mensaje-eliminar');
    const formConfirmado = document.getElementById('formEliminarConfirmado');
    const modalEliminar = document.getElementById('modalConfirmarEliminar');
    
    if (!mensajeEliminar || !formConfirmado || !modalEliminar) {
        mostrarError('No se encontraron los elementos del modal de confirmación');
        return;
    }
    
    mensajeEliminar.textContent = `¿Está seguro de eliminar la tarea "${nombreTarea}"?`;
    formConfirmado.action = form.action;
    
    const modal = new bootstrap.Modal(modalEliminar);
    
    // Remover listeners anteriores y agregar nuevo
    const nuevoForm = formConfirmado.cloneNode(true);
    formConfirmado.parentNode.replaceChild(nuevoForm, formConfirmado);
    
    nuevoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        modal.hide();
        form.submit();
    });
    
    modal.show();
}

function mostrarError(mensaje) {
    // Crear o usar un modal de error
    let modalError = document.getElementById('modalError');
    let mensajeError = document.getElementById('mensaje-error');
    
    if (!modalError) {
        modalError = crearModalError();
        mensajeError = document.getElementById('mensaje-error');
    }
    
    if (mensajeError) {
        mensajeError.textContent = mensaje;
        const modal = new bootstrap.Modal(modalError);
        modal.show();
    } else {
        // Fallback a alert si no se puede crear el modal
        console.error(mensaje);
        alert(mensaje);
    }
}

function crearModalError() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'modalError';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="mensaje-error"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    return modal;
}

// ============================================
// FUNCIONES PARA PANEL DE EVIDENCIAS DE TAREA
// ============================================

let tareaActualId = null;

function abrirPanelEvidenciasTarea(tareaId) {
    tareaActualId = tareaId;
    const overlay = document.getElementById('panelEvidenciasTareaOverlay');
    const panel = document.getElementById('panelEvidenciasTarea');

    const comentario = document.getElementById('nuevo-comentario-tarea');
    
    overlay.classList.add('show');
    panel.classList.add('show');
    document.body.style.overflow = 'hidden';

    //  console.log('ID de la tarea:', tareaId); <- debug
    
   
    renderizarPanelEvidenciasTarea(tareaId);
}

function cerrarPanelEvidenciasTarea() {
    const overlay = document.getElementById('panelEvidenciasTareaOverlay');
    const panel = document.getElementById('panelEvidenciasTarea');
    
    overlay.classList.remove('show');
    panel.classList.remove('show');
    document.body.style.overflow = '';
    tareaActualId = null;
}

function renderizarPanelEvidenciasTarea(tareaId) {
    const body = document.getElementById('panelEvidenciasTareaBody');
    
    // Cargar descripción y comentarios desde el backend
    cargarDescripcionTarea(tareaId);
    cargarComentariosTarea(tareaId);
    
    let html = `
        <div class="panel-evidencias-tarea-section">
            <h5><i class="bi bi-file-text"></i> Descripción</h5>
            <div id="descripcion-tarea-container">
                <div class="descripcion-box">
                    <p id="descripcion-tarea-texto">Cargando descripción...</p>
                </div>
                <a href="#" class="btn-editar-descripcion" onclick="editarDescripcionTarea(); return false;">
                    <i class="bi bi-pencil"></i> Editar Descripción
                </a>
                <div id="form-editar-descripcion-tarea" style="display: none;" class="mt-2">
                    <textarea id="nueva-descripcion-tarea" class="form-control" rows="4"></textarea>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-primary" onclick="guardarDescripcionTarea()">Guardar</button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cancelarEditarDescripcionTarea()">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="panel-evidencias-tarea-section">
            <h5>
                <i class="bi bi-chat-dots"></i> Comentarios
                <span class="comentarios-badge" id="comentarios-badge-tarea">0</span>
            </h5>
            <div id="lista-comentarios-tarea">
                <div class="comentarios-empty">
                    <i class="bi bi-chat-dots"></i>
                    <p>Cargando comentarios...</p>
                </div>
            </div>
            <div class="form-comentario-tarea">
                <div class="comentario-input-wrapper">
                    <input type="text" 
                           id="nuevo-comentario-tarea" 
                           class="form-control" 
                           placeholder="Escribe un comentario o @menciona a alguien..."
                           onkeypress="if(event.key === 'Enter') agregarComentarioTarea()">
                    <div class="comentario-input-icons">
                        <button type="button" title="Micrófono">
                            <i class="bi bi-mic"></i>
                        </button>
                        <button type="button" title="Emoji">
                            <i class="bi bi-emoji-smile"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-agregar-comentario" onclick="agregarComentarioTarea()">
                    Agregar Comentario <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
        
        <div class="panel-evidencias-tarea-section">
            <h5><i class="bi bi-folder"></i> Archivos y Evidencias</h5>
            <div id="lista-evidencias-tarea">
                <div class="evidencias-empty">
                    <p>Cargando evidencias...</p>
                </div>
            </div>
            <div class="form-evidencia-tarea">
                <div class="drop-zone" 
                     id="drop-zone-tarea"
                     onclick="document.getElementById('file-input-tarea').click()"
                     ondrop="handleDrop(event)"
                     ondragover="handleDragOver(event)"
                     ondragleave="handleDragLeave(event)">
                    <i class="bi bi-cloud-upload"></i>
                    <div class="drop-zone-text">Haz clic para subir o arrastra y suelta</div>
                    <div class="drop-zone-subtext">Imágenes, PDF o documentos Word/Excel (max. 10MB)</div>
                </div>
                <input type="file" 
                       id="file-input-tarea" 
                       style="display: none;" 
                       accept=".svg,.png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.gif,.webp"
                       onchange="handleFileSelect(event)">
            </div>
        </div>
    `;
    
    body.innerHTML = html;
    
    // Inicializar drag and drop para evidencias
    inicializarDragAndDrop();
    
    // Cargar evidencias desde el backend
    cargarEvidenciasTarea(tareaId);
}

function cargarDescripcionTarea(tareaId) {
    fetch(`/tareas/${tareaId}/descripcion`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarDescripcionTarea(data.descripcion || '');
        } else {
            mostrarDescripcionTarea('');
        }
    })
    .catch(error => {
        console.error('Error al cargar descripción:', error);
        mostrarDescripcionTarea('');
    });
}

function mostrarDescripcionTarea(descripcion) {
    const textoDescripcion = document.getElementById('descripcion-tarea-texto');
    const textareaDescripcion = document.getElementById('nueva-descripcion-tarea');
    
    if (textoDescripcion) {
        textoDescripcion.textContent = descripcion || 'Sin descripción agregada.';
    }
    
    if (textareaDescripcion) {
        textareaDescripcion.value = descripcion || '';
    }
}

function cargarComentariosTarea(tareaId) {
    fetch(`/tareas/${tareaId}/comentarios`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarComentariosTarea(data.comentarios);
        } else {
            mostrarComentariosTarea([]);
        }
    })
    .catch(error => {
        console.error('Error al cargar comentarios:', error);
        mostrarComentariosTarea([]);
    });
}

function mostrarComentariosTarea(comentarios) {
    const listaComentarios = document.getElementById('lista-comentarios-tarea');
    const badge = document.getElementById('comentarios-badge-tarea');
    
    if (!listaComentarios) return;
    
    // Actualizar badge
    if (badge) {
        badge.textContent = comentarios.length;
    }
    
    // Limpiar lista
    listaComentarios.innerHTML = '';
    
    if (comentarios.length === 0) {
        listaComentarios.innerHTML = `
            <div class="comentarios-empty">
                <i class="bi bi-chat-dots"></i>
                <p>No hay comentarios aún.</p>
            </div>
        `;
        return;
    }
    
    // Mostrar comentarios
    comentarios.forEach(comentario => {
        const comentarioDiv = document.createElement('div');
        comentarioDiv.className = 'comentario-item-tarea';
        comentarioDiv.innerHTML = `
            <div class="usuario-info">
                <strong>${escapeHtml(comentario.usuario_nombre)}</strong> - ${comentario.created_at_formatted}
            </div>
            <div>${escapeHtml(comentario.comentario)}</div>
        `;
        listaComentarios.appendChild(comentarioDiv);
    });
}

function inicializarDragAndDrop() {
    const dropZone = document.getElementById('drop-zone-tarea');
    const fileInput = document.getElementById('file-input-tarea');
    
    if (!dropZone || !fileInput) return;
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('highlight'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('highlight'), false);
    });

    dropZone.addEventListener('drop', handleDrop, false);
    fileInput.addEventListener('change', handleFileSelect, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFileSelect({ target: { files: files } });
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
}

function editarDescripcionTarea() {
    document.querySelector('.descripcion-box').style.display = 'none';
    document.querySelector('.btn-editar-descripcion').style.display = 'none';
    document.getElementById('form-editar-descripcion-tarea').style.display = 'block';
}

function cancelarEditarDescripcionTarea() {
    document.querySelector('.descripcion-box').style.display = 'block';
    document.querySelector('.btn-editar-descripcion').style.display = 'inline-flex';
    document.getElementById('form-editar-descripcion-tarea').style.display = 'none';
}

function guardarDescripcionTarea() {
    const nuevaDescripcion = document.getElementById('nueva-descripcion-tarea').value;
    
    if (!tareaActualId) {
        console.error('No hay tarea seleccionada');
        return;
    }
    
    console.log('Guardar descripción para tarea:', tareaActualId, nuevaDescripcion);
    
    // Petición AJAX al backend
    fetch(`/tareas/${tareaActualId}/descripcion`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            descripcion: nuevaDescripcion
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar descripción desde el backend
            cargarDescripcionTarea(tareaActualId);
            cancelarEditarDescripcionTarea();
            console.log('Descripción guardada correctamente');
        } else {
            console.error('Error:', data.message);
            alert('Error al guardar descripción: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error al guardar descripción:', error);
        alert('Error al guardar descripción. Por favor, intenta nuevamente.');
    });
}

function agregarComentarioTarea() {
    const comentario = document.getElementById('nuevo-comentario-tarea').value.trim();
    
    if (!comentario) {
        alert('Por favor, escribe un comentario.');
        return;
    }
    
    if (!tareaActualId) {
        console.error('No hay tarea seleccionada');
        return;
    }
    
    console.log('Agregar comentario para tarea:', tareaActualId, comentario);
    
    // Petición AJAX al backend
    fetch(`/tareas/${tareaActualId}/comentarios`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ comentario })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Limpiar el input
            document.getElementById('nuevo-comentario-tarea').value = '';
            
            // Recargar comentarios desde el backend
            cargarComentariosTarea(tareaActualId);
            
            console.log('Comentario agregado correctamente:', data);
        } else {
            alert('Error al agregar comentario: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error al agregar comentario:', error);
        alert('Error al agregar comentario. Por favor, intenta nuevamente.');
    });
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        procesarArchivo(file);
    }
}

function handleDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById('drop-zone-tarea').classList.add('dragover');
}

function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById('drop-zone-tarea').classList.remove('dragover');
}

function handleDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById('drop-zone-tarea').classList.remove('dragover');
    
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        procesarArchivo(files[0]);
    }
}

function procesarArchivo(file) {
    if (!tareaActualId) {
        console.error('No hay tarea seleccionada');
        return;
    }

    // Validar tipo de archivo
    const extension = file.name.split('.').pop().toLowerCase();
    const extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    
    if (!extensionesPermitidas.includes(extension)) {
        alert('Tipo de archivo no permitido. Solo imágenes (JPG, PNG, GIF, SVG, WEBP), PDF o documentos Word/Excel (DOC, DOCX, XLS, XLSX, PPT, PPTX).');
        return;
    }
    
    // Validar tamaño (10MB)
    if (file.size > 10 * 1024 * 1024) {
        alert('El archivo es demasiado grande. Máximo 10MB.');
        return;
    }
    
    console.log('Subir evidencia para tarea:', tareaActualId, file.name);
    
    // Crear FormData para enviar el archivo
    const formData = new FormData();
    formData.append('archivo', file);
    
    // Mostrar indicador de carga
    const listaEvidencias = document.getElementById('lista-evidencias-tarea');
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'evidencia-item-tarea';
    loadingDiv.innerHTML = `
        <div class="text-center py-2">
            <span class="spinner-border spinner-border-sm" role="status"></span>
            <span class="ms-2">Subiendo ${escapeHtml(file.name)}...</span>
        </div>
    `;
    listaEvidencias.insertBefore(loadingDiv, listaEvidencias.firstChild);
    
    // Subir archivo al backend
    fetch(`/tareas/${tareaActualId}/evidencias`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Remover indicador de carga
        loadingDiv.remove();
        
        if (data.success) {
            // Recargar evidencias desde el backend
            cargarEvidenciasTarea(tareaActualId);
            console.log('Evidencia subida correctamente:', data);
        } else {
            alert('Error al subir evidencia: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        loadingDiv.remove();
        console.error('Error al subir evidencia:', error);
        alert('Error al subir evidencia. Por favor, intenta nuevamente.');
    });
    
    // Limpiar el input
    document.getElementById('file-input-tarea').value = '';
}

function cargarEvidenciasTarea(tareaId) {
    fetch(`/tareas/${tareaId}/evidencias`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarEvidenciasTarea(data.evidencias);
        } else {
            mostrarEvidenciasTarea([]);
        }
    })
    .catch(error => {
        console.error('Error al cargar evidencias:', error);
        mostrarEvidenciasTarea([]);
    });
}

function mostrarEvidenciasTarea(evidencias) {
    const listaEvidencias = document.getElementById('lista-evidencias-tarea');
    
    if (!listaEvidencias) return;
    
    // Limpiar lista
    listaEvidencias.innerHTML = '';
    
    if (evidencias.length === 0) {
        listaEvidencias.innerHTML = `
            <div class="evidencias-empty">
                <p>No hay evidencias subidas aún. Sube capturas de pantalla o documentos para validar la tarea.</p>
            </div>
        `;
        return;
    }
    
    // Mostrar evidencias
    evidencias.forEach(evidencia => {
        const evidenciaDiv = document.createElement('div');
        evidenciaDiv.className = 'evidencia-item-tarea';
        evidenciaDiv.dataset.evidenciaId = evidencia.id;
        
        let contenidoEvidencia = '';
        // Usar url_directa si existe (para imágenes y PDFs), sino usar url (endpoint download)
        const urlDirecta = evidencia.url_directa || null;
        const urlEnlace = evidencia.url;
        const esVisualizable = evidencia.es_visualizable || false;
        
        if (evidencia.es_imagen) {
            // Para imágenes, usar url_directa directamente en el src (como technical-report-bigbag)
            // Solo mostrar imagen si tenemos url_directa válida
            if (urlDirecta) {
                contenidoEvidencia = `
                    <div class="mb-2">
                        <img src="${escapeHtml(urlDirecta)}" alt="${escapeHtml(evidencia.nombre_archivo)}" 
                             style="max-width: 100%; max-height: 200px; border-radius: 4px; cursor: pointer;"
                             onclick="window.open('${escapeHtml(urlDirecta)}', '_blank')"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'200\\' height=\\'200\\'%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'">
                    </div>
                `;
            } else {
                contenidoEvidencia = `
                    <div class="mb-2">
                        <p class="text-muted">Imagen no disponible</p>
                    </div>
                `;
            }
        } else {
            contenidoEvidencia = `
                <div>
                    <i class="bi ${evidencia.icono}"></i> 
                    <span class="text-decoration-none">
                        ${escapeHtml(evidencia.nombre_archivo)}
                    </span>
                </div>
            `;
        }
        
        // Determinar qué botón mostrar según si es visualizable o no
        let botonAccion = '';
        if (esVisualizable && urlDirecta) {
            // Para imágenes y PDFs: botón "Ver" (solo si tenemos url_directa)
            botonAccion = `
                <a href="${escapeHtml(urlDirecta)}" target="_blank" 
                   class="btn btn-sm btn-outline-primary" title="Ver">
                    <i class="bi bi-eye"></i> Ver
                </a>
            `;
        } else if (esVisualizable) {
            // Si es visualizable pero no hay url_directa, usar el endpoint download
            botonAccion = `
                <a href="${escapeHtml(urlEnlace)}" target="_blank" 
                   class="btn btn-sm btn-outline-primary" title="Ver">
                    <i class="bi bi-eye"></i> Ver
                </a>
            `;
        } else {
            // Para Word, Excel, etc.: botón "Descargar"
            botonAccion = `
                <a href="${escapeHtml(urlEnlace)}" download="${escapeHtml(evidencia.nombre_archivo)}"
                   class="btn btn-sm btn-outline-success" title="Descargar">
                    <i class="bi bi-download"></i> Descargar
                </a>
            `;
        }
        
        evidenciaDiv.innerHTML = `
            <div class="usuario-info">
                <strong>Archivo</strong> - ${evidencia.created_at_formatted}
            </div>
            ${contenidoEvidencia}
            <div class="evidencia-acciones">
                ${botonAccion}
                <button type="button" 
                        class="btn btn-sm btn-outline-danger" 
                        onclick="eliminarEvidenciaTarea(${evidencia.id})"
                        title="Eliminar">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        `;
        
        listaEvidencias.appendChild(evidenciaDiv);
    });
}

function eliminarEvidenciaTarea(evidenciaId) {
    if (!tareaActualId) {
        console.error('No hay tarea seleccionada');
        return;
    }
    
    if (!confirm('¿Estás seguro de eliminar esta evidencia?')) {
        return;
    }
    
    console.log('Eliminar evidencia:', evidenciaId);
    
    // Encontrar el elemento en el DOM
    const evidenciaElement = document.querySelector(`[data-evidencia-id="${evidenciaId}"]`);
    
    // Mostrar indicador de eliminación
    if (evidenciaElement) {
        evidenciaElement.style.opacity = '0.5';
        evidenciaElement.style.pointerEvents = 'none';
    }
    
    // Eliminar desde el backend
    fetch(`/tareas/${tareaActualId}/evidencias/${evidenciaId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar evidencias desde el backend
            cargarEvidenciasTarea(tareaActualId);
            console.log('Evidencia eliminada correctamente');
        } else {
            alert('Error al eliminar evidencia: ' + (data.message || 'Error desconocido'));
            // Restaurar elemento si falló
            if (evidenciaElement) {
                evidenciaElement.style.opacity = '1';
                evidenciaElement.style.pointerEvents = 'auto';
            }
        }
    })
    .catch(error => {
        console.error('Error al eliminar evidencia:', error);
        alert('Error al eliminar evidencia. Por favor, intenta nuevamente.');
        // Restaurar elemento si falló
        if (evidenciaElement) {
            evidenciaElement.style.opacity = '1';
            evidenciaElement.style.pointerEvents = 'auto';
        }
    });
    const listaEvidencias = document.getElementById('lista-evidencias-tarea');
    if (listaEvidencias.children.length === 0) {
        listaEvidencias.innerHTML = '<div class="evidencias-empty"><p>No hay evidencias subidas aún. Sube capturas de pantalla o documentos para validar la tarea.</p></div>';
    }
    
    // Aquí se hará la petición AJAX al backend cuando esté listo
    // fetch(`/tareas/${tareaActualId}/evidencias/${evidenciaId}`, { method: 'DELETE', ... })
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================
// FUNCIONES PARA CALENDARIO
// ============================================

function cambiarMes(direccion) {
    event.preventDefault();
    
    // Obtener mes y año actual desde la URL o valores por defecto
    const urlParams = new URLSearchParams(window.location.search);
    let mes = parseInt(urlParams.get('mes')) || {{ $mes ?? \Carbon\Carbon::now('America/Bogota')->month }};
    let ano = parseInt(urlParams.get('ano')) || {{ $ano ?? \Carbon\Carbon::now('America/Bogota')->year }};
    
    // Calcular nuevo mes y año
    mes += direccion;
    
    // Manejar cambio de año
    if (mes < 1) {
        mes = 12;
        ano -= 1;
    } else if (mes > 12) {
        mes = 1;
        ano += 1;
    }
    
    // Construir nueva URL
    const proyectoId = {{ $proyectoId }};
    const nuevaUrl = `{{ route('actividades.index', $proyectoId) }}?mes=${mes}&ano=${ano}`;
    
    // Redirigir
    window.location.href = nuevaUrl;
}

// Activar pestaña correcta al cargar
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tieneParametros = urlParams.has('mes') || urlParams.has('ano');
    
    if (tieneParametros) {
        // Activar pestaña de Calendario
        const tabCalendario = document.querySelector('a[href*="mes="]');
        const tabFases = document.querySelector('a[href*="actividades"]:not([href*="mes="])');
        
        if (tabCalendario && tabFases) {
            tabCalendario.classList.add('active');
            tabFases.classList.remove('active');
            
            const paneCalendario = document.getElementById('calendario');
            const paneFases = document.getElementById('fases');
            
            if (paneCalendario && paneFases) {
                paneCalendario.classList.add('show', 'active');
                paneFases.classList.remove('show', 'active');
            }
        }
    }
});
</script>

@endsection
