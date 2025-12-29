@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<style>
    /* Reset y base */
    body {
        background: #f5f5f5 !important;
    }

    body .container {
        background: #f5f5f5;
        max-width: 1400px;
        padding: 1.3rem;
    }

    /* Header */
    .inicio-header {
        margin-bottom: 1.3rem;
    }

    .inicio-title {
        font-size: 1.32rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.44rem;
        line-height: 1.3;
    }

    .inicio-subtitle {
        font-size: 0.77rem;
        color: #6c757d;
        line-height: 1.3;
    }

    /* Tarjetas de sección */
    .section-card {
        background: white;
        border-radius: 8px;
        padding: 1.1rem;
        margin-top: 2%;
        margin-bottom: 1.3rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: none;
    }

    .section-title {
        font-size: 0.88rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.88rem;
        display: flex;
        align-items: center;
        gap: 0.44rem;
        line-height: 1.3;
    }

    .section-title i {
        color: #6c757d;
        font-size: 0.99rem;
    }

    .section-title .bi-plus-circle-fill {
        background: #6f42c1;
        color: white !important;
        width: 21px;
        height: 21px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.77rem;
        padding: 0;
    }

    /* Grid de dos columnas */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.3rem;
    }

    @media (min-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
            align-items: start;
            column-gap: 1.3rem;
            row-gap: 0;
        }

        /* Eliminar margin-bottom de las tarjetas dentro del grid */
        .dashboard-grid>.section-card {
            margin-bottom: 0;
        }

        /* Columna izquierda: Resumen de Tareas - ocupa ambas filas */
        .dashboard-grid>.section-card:first-child {
            grid-row: 1 / span 2;
            display: flex;
            flex-direction: column;
            align-self: stretch;
        }

        /* Primera tarjeta de la derecha: Nueva Tarea Rápida */
        .dashboard-grid>.section-card:nth-child(2) {
            grid-row: 1;
            display: flex;
            flex-direction: column;
            align-self: start;
            margin-bottom: 0;
            border-radius: 8px 8px 0 0;
        }

        /* Segunda tarjeta de la derecha: Mis Tareas Independientes */
        .dashboard-grid>.section-card:nth-child(3) {
            grid-row: 2;
            display: flex;
            flex-direction: column;
            align-self: start;
            margin-top: 0;
            border-radius: 0 0 8px 8px;
            border-top: 1px solid #e9ecef;
        }
    }

    /* Media query para resolución 1280x585 */
    @media (min-width: 1270px) and (max-width: 1290px) and (min-height: 575px) and (max-height: 595px) {
        body .container {
            padding: 0.7rem;
        }

        .inicio-header {
            margin-bottom: 0.7rem;
        }

        .inicio-title {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .inicio-subtitle {
            font-size: 0.65rem;
            line-height: 1.2;
        }

        .section-card {
            padding: 0.6rem;
            margin-top: 1%;
            margin-bottom: 0.7rem;
        }

        .section-title {
            font-size: 0.7rem;
            margin-bottom: 0.5rem;
            gap: 0.3rem;
            line-height: 1.2;
        }

        .section-title i {
            font-size: 0.8rem;
        }

        .section-title .bi-plus-circle-fill {
            width: 17px;
            height: 17px;
            font-size: 0.65rem;
        }

        .dashboard-grid {
            gap: 0.7rem;
        }

        .tareas-tabs {
            margin-bottom: 0.5rem;
        }

        .tareas-tabs .nav-link {
            font-size: 0.65rem;
            padding: 0.3rem 0.45rem;
            gap: 0.3rem;
            line-height: 1.2;
        }

        .tab-badge {
            min-width: 17px;
            height: 14px;
            padding: 0 5px;
            font-size: 0.55rem;
            line-height: 1.1;
        }

        .tarea-item-dashboard {
            padding: 0.4rem 0;
            gap: 0.45rem;
        }

        .tarea-checkbox-dash {
            width: 14px;
            height: 14px;
            margin-top: 0.08rem;
        }

        .tarea-checkbox-dash:checked::after {
            font-size: 9px;
        }

        .tarea-nombre-dash {
            font-size: 0.7rem;
            margin-bottom: 0.2rem;
            line-height: 1.2;
        }

        .tarea-subtask {
            font-size: 0.6rem;
            margin-top: 0.15rem;
            margin-bottom: 0.25rem;
            gap: 0.25rem;
            line-height: 1.2;
        }

        .tarea-meta-dash {
            gap: 0.3rem;
            font-size: 0.55rem;
        }

        .tarea-proyecto {
            gap: 0.18rem;
            font-size: 0.55rem;
            line-height: 1.2;
        }

        .tarea-proyecto i {
            font-size: 0.55rem;
        }

        .badge-dashboard {
            padding: 0.12rem 0.3rem;
            font-size: 0.55rem;
            line-height: 1.1;
        }

        .badge-vencida {
            font-size: 0.5rem;
            padding: 0.08rem 0.3rem;
            line-height: 1.1;
        }

        .ver-todas-link {
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            font-size: 0.65rem;
            line-height: 1.2;
        }

        .form-tarea-rapida .form-group {
            gap: 0.3rem;
        }

        .form-tarea-rapida .form-control,
        .form-tarea-rapida .form-select {
            font-size: 0.65rem;
            padding: 0.3rem 0.45rem;
            height: 28px;
            line-height: 1.2;
        }

        .form-tarea-rapida .form-select {
            min-width: 110px;
            padding-right: 1.8rem;
        }

        .form-tarea-rapida input[type="date"] {
            min-width: 110px;
        }

        .tareas-independientes-header {
            margin-bottom: 0.5rem;
        }

        .tareas-independientes-title {
            font-size: 0.7rem;
            gap: 0.25rem;
            line-height: 1.2;
        }

        .tareas-independientes-title i {
            font-size: 0.75rem;
        }

        .tareas-independientes-count {
            font-size: 0.6rem;
            padding: 0.18rem 0.45rem;
            line-height: 1.1;
        }

        .tarea-independiente-item {
            padding: 0.4rem 0;
            gap: 0.45rem;
        }

        .tarea-independiente-checkbox {
            width: 14px;
            height: 14px;
            margin-top: 0.08rem;
        }

        .tarea-independiente-checkbox:checked::after {
            font-size: 9px;
        }

        .tarea-independiente-nombre {
            font-size: 0.7rem;
            margin-bottom: 0.2rem;
            line-height: 1.2;
        }

        .tarea-independiente-meta {
            gap: 0.3rem;
            font-size: 0.55rem;
            margin-top: 0.15rem;
        }

        .tarea-fecha-independiente {
            gap: 0.18rem;
            font-size: 0.55rem;
            line-height: 1.2;
        }

        .tarea-fecha-independiente i {
            font-size: 0.55rem;
        }

        .empty-state {
            padding: 1rem;
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            font-size: 0.7rem;
            line-height: 1.2;
        }

        .proyectos-grid {
            gap: 0.5rem;
        }

        .proyecto-card {
            padding: 0.6rem;
        }

        .proyecto-nombre {
            font-size: 0.7rem;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .proyecto-meta {
            font-size: 0.55rem;
            gap: 0.3rem;
            line-height: 1.2;
        }
    }

    /* Media query para resolución de portátil 1422x650 */
    @media (min-width: 1400px) and (max-width: 1444px) and (min-height: 600px) and (max-height: 700px) {
        body .container {
            padding: 1.4rem;
        }

        .inicio-header {
            margin-bottom: 1.4rem;
        }

        .inicio-title {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }

        .inicio-subtitle {
            font-size: 0.9rem;
        }

        .section-card {
            padding: 1.2rem;
            margin-bottom: 1.4rem;
        }

        .section-title {
            font-size: 0.95rem;
            margin-bottom: 0.95rem;
        }

        .section-title i {
            font-size: 1.05rem;
        }

        .section-title .bi-plus-circle-fill {
            width: 22px;
            height: 22px;
            font-size: 0.8rem;
        }

        .dashboard-grid {
            gap: 1.4rem;
        }

        .tareas-tabs {
            margin-bottom: 0.95rem;
        }

        .tareas-tabs .nav-link {
            font-size: 0.8rem;
            padding: 0.48rem 0.7rem;
        }

        .tab-badge {
            min-width: 22px;
            height: 19px;
            padding: 0 7px;
            font-size: 0.7rem;
        }

        .tarea-item-dashboard {
            padding: 0.7rem 0;
            gap: 0.7rem;
        }

        .tarea-checkbox-dash {
            width: 17px;
            height: 17px;
        }

        .tarea-checkbox-dash:checked::after {
            font-size: 10.5px;
        }

        .tarea-nombre-dash {
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }

        .tarea-subtask {
            font-size: 0.75rem;
            margin-top: 0.24rem;
            margin-bottom: 0.48rem;
        }

        .tarea-meta-dash {
            gap: 0.48rem;
            font-size: 0.7rem;
        }

        .tarea-proyecto {
            gap: 0.24rem;
            font-size: 0.7rem;
        }

        .badge-dashboard {
            padding: 0.18rem 0.48rem;
            font-size: 0.7rem;
        }

        .badge-vencida {
            font-size: 0.65rem;
            padding: 0.12rem 0.48rem;
        }

        .ver-todas-link {
            margin-top: 0.95rem;
            padding-top: 0.95rem;
            font-size: 0.8rem;
        }

        .form-tarea-rapida .form-control,
        .form-tarea-rapida .form-select {
            font-size: 0.8rem;
            padding: 0.48rem 0.7rem;
            height: 35px;
        }

        .form-tarea-rapida .form-select {
            min-width: 140px;
            padding-right: 2.3rem;
        }

        .form-tarea-rapida input[type="date"] {
            min-width: 140px;
        }

        .tareas-independientes-header {
            margin-bottom: 0.95rem;
        }

        .tareas-independientes-title {
            font-size: 0.9rem;
        }

        .tareas-independientes-title i {
            font-size: 0.95rem;
        }

        .tareas-independientes-count {
            font-size: 0.75rem;
            padding: 0.24rem 0.7rem;
        }

        .tarea-independiente-item {
            padding: 0.7rem 0;
            gap: 0.7rem;
        }

        .tarea-independiente-checkbox {
            width: 19px;
            height: 19px;
        }

        .tarea-independiente-checkbox:checked::after {
            font-size: 11.5px;
        }

        .tarea-independiente-nombre {
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }

        .tarea-independiente-meta {
            gap: 0.48rem;
            font-size: 0.7rem;
            margin-top: 0.24rem;
        }

        .tarea-fecha-independiente {
            gap: 0.24rem;
            font-size: 0.7rem;
        }

        .tarea-fecha-independiente i {
            font-size: 0.7rem;
        }

        .empty-state {
            padding: 1.9rem;
        }

        .empty-state i {
            font-size: 2.8rem;
            margin-bottom: 0.95rem;
        }

        .empty-state p {
            font-size: 0.95rem;
        }

        .proyectos-grid {
            gap: 0.95rem;
        }

        .proyecto-card {
            padding: 1.2rem;
        }

        .proyecto-nombre {
            font-size: 0.95rem;
            margin-bottom: 0.48rem;
        }

        .proyecto-meta {
            font-size: 0.7rem;
            gap: 0.48rem;
        }
    }

    .dashboard-grid-full {
        grid-column: 1 / -1;
    }

    /* Tabs */
    .tareas-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 0.88rem;
        border-bottom: none;
        padding: 0;
    }

    .tareas-tabs .nav-item {
        margin: 0;
    }

    .tareas-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        font-weight: 500;
        font-size: 0.77rem;
        padding: 0.44rem 0.66rem;
        display: flex;
        align-items: center;
        gap: 0.44rem;
        background: transparent;
        text-decoration: none;
        transition: all 0.2s;
        line-height: 1.3;
    }

    .tareas-tabs .nav-link:hover {
        color: #212529;
        border-bottom-color: transparent;
    }

    .tareas-tabs .nav-link.active {
        color: #212529;
        font-weight: 600;
    }

    .tareas-tabs .nav-link.active.ultimas {
        border-bottom-color: #6c757d;
    }

    .tareas-tabs .nav-link.active.proximas {
        border-bottom-color: #ffc107;
    }

    .tareas-tabs .nav-link.active.vencidas {
        border-bottom-color: #dc3545;
    }

    .tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 21px;
        height: 18px;
        padding: 0 7px;
        border-radius: 9px;
        font-size: 0.66rem;
        font-weight: 600;
        background: #e9ecef;
        color: #6c757d;
        line-height: 1.2;
    }

    .tab-badge.warning {
        background: #ffc107;
        color: #212529;
    }

    .tab-badge.danger {
        background: #dc3545;
        color: white;
    }

    /* Items de tarea */
    .tarea-item-dashboard {
        padding: 0.66rem 0;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: flex-start;
        gap: 0.66rem;
    }

    .tarea-item-dashboard:last-child {
        border-bottom: none;
    }

    .tarea-checkbox-dash {
        width: 18px;
        height: 18px;
        margin-top: 0.11rem;
        cursor: pointer;
        flex-shrink: 0;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: white;
        position: relative;
    }

    .tarea-checkbox-dash:checked {
        border-radius: 4px;
        background: #6f42c1;
        border-color: #6f42c1;
    }

    .tarea-checkbox-dash:checked::after {
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

    .tarea-info-dash {
        flex: 1;
        min-width: 0;
    }

    .tarea-nombre-dash {
        font-size: 0.825rem;
        font-weight: 500;
        color: #212529;
        margin-bottom: 0.33rem;
        line-height: 1.3;
    }

    .tarea-nombre-dash.completada {
        text-decoration: line-through;
        color: #6c757d;
    }

    .tarea-subtask {
        font-size: 0.715rem;
        color: #6c757d;
        margin-top: 0.22rem;
        margin-bottom: 0.44rem;
        display: flex;
        align-items: center;
        gap: 0.33rem;
        line-height: 1.3;
    }

    .tarea-meta-dash {
        display: flex;
        flex-wrap: wrap;
        gap: 0.44rem;
        align-items: center;
        font-size: 0.66rem;
    }

    .tarea-proyecto {
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.22rem;
        font-size: 0.66rem;
        line-height: 1.3;
    }

    .tarea-proyecto i {
        color: #6c757d;
        font-size: 0.66rem;
    }

    /* Badges */
    .badge-dashboard {
        padding: 0.165rem 0.44rem;
        border-radius: 11px;
        font-size: 0.66rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        line-height: 1.2;
    }

    .badge-prioridad-alta {
        background: #F8D7DA;
        color: #721C24;
    }

    .badge-prioridad-media {
        background: #B3D9FF;
        color: #0066CC;
    }

    .badge-prioridad-baja {
        background: #E2E3E5;
        color: #41464B;
    }

    .badge-estado-completado {
        background: #D4EDDA;
        color: #212529;
    }

    .badge-estado-en_progreso {
        background: #FFE69C;
        color: #212529;
    }

    .badge-estado-pendiente {
        background: #E9ECEF;
        color: #6C757D;
    }

    .badge-vencida {
        background: #F8D7DA;
        color: #721C24;
        font-weight: 600;
        font-size: 0.605rem;
        padding: 0.11rem 0.44rem;
        line-height: 1.2;
    }

    /* Link ver todas */
    .ver-todas-link {
        display: block;
        text-align: center;
        margin-top: 0.88rem;
        padding-top: 0.88rem;
        color: #0D6EFD;
        text-decoration: none;
        font-size: 0.77rem;
        font-weight: 500;
        border-top: 1px solid #f0f0f0;
        line-height: 1.3;
    }

    .ver-todas-link:hover {
        color: #0B5ED7;
        text-decoration: underline;
    }

    /* Formulario tarea rápida */
    .form-tarea-rapida {
        margin-bottom: 0;
    }

    .form-tarea-rapida .form-group {
        display: flex;
        gap: 0.44rem;
        align-items: stretch;
    }

    .form-tarea-rapida .form-control,
    .form-tarea-rapida .form-select {
        font-size: 0.77rem;
        padding: 0.44rem 0.66rem;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        height: 33px;
        line-height: 1.3;
    }

    .form-tarea-rapida #input-tarea-rapida {
        flex: 1;
        min-width: 0;
    }

    .form-tarea-rapida .form-select {
        min-width: 90px;
        max-width: 90px;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.66rem center;
        background-size: 14px 11px;
        padding-right: 2.2rem;
        appearance: none;
    }

    .form-tarea-rapida input[type="date"] {
        width: 38px;
        min-width: 38px;
        max-width: 38px;
        padding: 0.44rem 0;
        text-indent: -9999px;
        overflow: hidden;
        position: relative;
    }

    .form-tarea-rapida input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 1;
        cursor: pointer;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 16px;
        height: 16px;
    }

    /* Tareas independientes */
    .tareas-independientes-list {
        margin-top: 0;
    }

    .tareas-independientes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.88rem;
    }

    .tareas-independientes-title {
        font-size: 0.825rem;
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 0.33rem;
        line-height: 1.3;
    }

    .tareas-independientes-title i {
        color: #6c757d;
        font-size: 0.88rem;
    }


    .tareas-independientes-count {
        font-size: 0.715rem;
        color: #6c757d;
        background: #e9ecef;
        padding: 0.22rem 0.66rem;
        border-radius: 11px;
        font-weight: 500;
        line-height: 1.2;
    }

    .tarea-independiente-item {
        padding: 0.66rem 0;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: flex-start;
        gap: 0.66rem;
    }

    .tarea-independiente-item:last-child {
        border-bottom: none;
    }

    .tarea-independiente-checkbox {
        width: 18px;
        height: 18px;
        margin-top: 0.11rem;
        cursor: pointer;
        flex-shrink: 0;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: white;
        position: relative;
    }

    .tarea-independiente-checkbox:checked {
        border-radius: 4px;
        background: #6f42c1;
        border-color: #6f42c1;
    }

    .tarea-independiente-checkbox:checked::after {
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

    .tarea-independiente-content {
        flex: 1;
        min-width: 0;
    }

    .tarea-independiente-nombre {
        font-size: 0.825rem;
        font-weight: 500;
        color: #212529;
        margin-bottom: 0.33rem;
        line-height: 1.3;
    }

    .tarea-independiente-nombre.completada {
        text-decoration: line-through;
        color: #6c757d;
    }

    .tarea-independiente-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.44rem;
        align-items: center;
        font-size: 0.66rem;
        margin-top: 0.22rem;
    }

    .tarea-fecha-independiente {
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.22rem;
        font-size: 0.66rem;
        line-height: 1.3;
    }

    .tarea-fecha-independiente i {
        font-size: 0.66rem;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 1.76rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 2.64rem;
        margin-bottom: 0.88rem;
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 0.88rem;
        line-height: 1.3;
    }

    /* Proyectos */
    .proyectos-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.88rem;
    }

    @media (min-width: 768px) {
        .proyectos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 992px) {
        .proyectos-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .proyecto-card {
        background: linear-gradient(135deg, var(--proyecto-color, #0D6EFD) 0%, var(--proyecto-color-dark, #0B5ED7) 100%);
        border-radius: 12px;
        padding: 1.1rem;
        color: white;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        text-decoration: none;
        display: block;
    }

    .proyecto-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        color: white;
        text-decoration: none;
    }

    .proyecto-nombre {
        font-size: 0.88rem;
        font-weight: 600;
        margin-bottom: 0.44rem;
        line-height: 1.3;
    }

    .proyecto-meta {
        font-size: 0.66rem;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 0.44rem;
        line-height: 1.3;
    }
</style>

<div class="container">

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

    {{-- Layout de dos columnas --}}
    <div class="dashboard-grid">
        {{-- Columna Izquierda: Resumen de Tareas --}}
        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-list-check"></i>
                Resumen de Tareas
            </h2>

            {{-- Tabs --}}
            <ul class="nav nav-tabs tareas-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $tabActivo === 'ultimas' ? 'active ultimas' : '' }}"
                        href="{{ route('inicio') }}?tab=ultimas"
                        role="tab">
                        Últimas Tareas
                        <span class="tab-badge">{{ $totalUltimas }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $tabActivo === 'proximas' ? 'active proximas' : '' }}"
                        href="{{ route('inicio') }}?tab=proximas"
                        role="tab">
                        Próximas
                        <span class="tab-badge warning">{{ $totalProximas }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $tabActivo === 'vencidas' ? 'active vencidas' : '' }}"
                        href="{{ route('inicio') }}?tab=vencidas"
                        role="tab">
                        Vencidas
                        <span class="tab-badge danger">{{ $totalVencidas }}</span>
                    </a>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content">
                {{-- Últimas Tareas --}}
                @if($tabActivo === 'ultimas')
                <div class="tab-pane fade show active" id="ultimas" role="tabpanel">
                    @if($ultimasTareas && $ultimasTareas->count() > 0)
                    @foreach($ultimasTareas as $tarea)
                    @include('inicio._tarea_item', ['tarea' => $tarea])
                    @endforeach

                    {{-- Paginador --}}
                    @if($ultimasTareas->hasPages())
                    <div class="mt-3">
                        @include('partials.pagination', ['paginator' => $ultimasTareas])
                    </div>
                    @endif
                    @else
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>No hay tareas recientes</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Próximas a Vencer --}}
                @if($tabActivo === 'proximas')
                <div class="tab-pane fade show active" id="proximas" role="tabpanel">
                    @if($tareasProximas && $tareasProximas->count() > 0)
                    @foreach($tareasProximas as $tarea)
                    @include('inicio._tarea_item', ['tarea' => $tarea])
                    @endforeach

                    {{-- Paginador --}}
                    @if($tareasProximas->hasPages())
                    <div class="mt-3">
                        @include('partials.pagination', ['paginator' => $tareasProximas])
                    </div>
                    @endif
                    @else
                    <div class="empty-state">
                        <i class="bi bi-calendar-check"></i>
                        <p>No hay tareas próximas a vencer</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Tareas Vencidas --}}
                @if($tabActivo === 'vencidas')
                <div class="tab-pane fade show active" id="vencidas" role="tabpanel">
                    @if($tareasVencidas && $tareasVencidas->count() > 0)
                    @foreach($tareasVencidas as $tarea)
                    @include('inicio._tarea_item', ['tarea' => $tarea, 'vencida' => true])
                    @endforeach

                    {{-- Paginador --}}
                    @if($tareasVencidas->hasPages())
                    <div class="mt-3">
                        @include('partials.pagination', ['paginator' => $tareasVencidas])
                    </div>
                    @endif
                    @else
                    <div class="empty-state">
                        <i class="bi bi-check2-all"></i>
                        <p>¡Excelente! No tienes tareas vencidas</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Link Ver todas las tareas --}}
            <a href="{{ route('inicio') }}" class="ver-todas-link">
                Ver todas las tareas
            </a>
        </div>

        {{-- Columna Derecha: Nueva Tarea Rápida --}}
        <div>
            @permiso('crear tarea')
            <div class="section-card">
                <h2 class="section-title">
                    <i class="bi bi-plus-circle-fill"></i>
                    Nueva Tarea Rápida
                </h2>
                <div class="form-tarea-rapida">
                    <form action="{{ route('tareas.store') }}" method="POST" id="form-tarea-rapida">
                        @csrf
                        <input type="hidden" name="es_independiente" value="1">

                        <div class="form-group">
                            <input type="text"
                                name="nombre"
                                class="form-control"
                                placeholder="¿Qué necesitas hacer hoy?"
                                required
                                id="input-tarea-rapida">
                            <select name="prioridad" class="form-select" id="select-prioridad">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                            </select>
                            <input type="date" name="fecha_fin" class="form-control" placeholder="mm/dd/yyyy" id="input-fecha-fin">
                        </div>
                    </form>
                </div>
            </div>

            {{-- Columna Derecha: Tareas Independientes --}}
            <div class="section-card">
                <div class="tareas-independientes-header">
                    <div class="tareas-independientes-title">
                        <i class="bi bi-list-check"></i>
                        Mis Tareas Independientes
                    </div>
                    <span class="tareas-independientes-count">{{ $tareasIndependientes->count() }} tareas</span>
                </div>

                @if($tareasIndependientes->count() > 0)
                <div class="tareas-independientes-list">
                    @foreach($tareasIndependientes as $tarea)
                    <div class="tarea-independiente-item {{ $tarea->estaCompletada() ? 'completada' : '' }}">
                        <input type="checkbox"
                            class="tarea-independiente-checkbox"
                            {{ $tarea->estaCompletada() ? 'checked' : '' }}
                            onchange="toggleTareaCompletada({{ $tarea->id }}, this.checked)">

                        <div class="tarea-independiente-content">
                            <div class="tarea-independiente-nombre {{ $tarea->estaCompletada() ? 'completada' : '' }}">
                                {{ $tarea->nombre }}
                            </div>

                            <div class="tarea-independiente-meta">
                                {{-- Prioridad --}}
                                <span class="badge-dashboard badge-prioridad-{{ $tarea->prioridad ?? 'media' }}">
                                    {{ ucfirst($tarea->prioridad ?? 'media') }}
                                </span>

                                {{-- Estado --}}
                                <span class="badge-dashboard badge-estado-{{ $tarea->estado }}">
                                    @if($tarea->estado === 'completado')
                                    Completado
                                    @elseif($tarea->estado === 'en_progreso')
                                    En Progreso
                                    @else
                                    Pendiente
                                    @endif
                                </span>

                                {{-- Fecha --}}
                                @if($tarea->fecha_fin)
                                <span class="tarea-fecha-independiente">
                                    <i class="bi bi-calendar3"></i>
                                    {{ \Carbon\Carbon::parse($tarea->fecha_fin)->format('d M Y') }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state" style="padding: 1.32rem;">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-0">No tienes tareas independientes</p>
                    <small class="text-muted">Crea una tarea rápida usando el formulario de arriba</small>
                </div>
                @endif
            </div>

             {{-- Sección: Proyectos --}}
    <div class="section-card dashboard-grid-full">
        <h2 class="section-title">
            <i class="bi bi-folder"></i>
            Mis Proyectos
        </h2>
        @if($proyectos->count() > 0)
        <div class="proyectos-grid">
            @foreach($proyectos as $proyecto)
            <a href="{{ route('actividades.index', $proyecto->id) }}"
                class="proyecto-card"
                style="--proyecto-color: {{ $proyecto->color }}; --proyecto-color-dark: {{ $proyecto->color }}">
                <div class="proyecto-nombre">{{ $proyecto->nombre }}</div>
                <div class="proyecto-meta">
                    <i class="bi bi-list-task"></i>
                    <span>{{ $proyecto->actividades->sum(function($act) { return $act->tareas->count(); }) }} tareas</span>
                    <span>•</span>
                    <span>{{ ucfirst($proyecto->estado) }}</span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-folder-x"></i>
            <p>No tienes proyectos asignados</p>
            @permiso('crear proyecto')
            <a href="{{ route('proyectos.create') }}" class="btn btn-primary btn-sm mt-2">
                Crear Proyecto
            </a>
            @endpermiso
        </div>
        @endif
    </div>
        </div>
        @endpermiso
    </div>

   
</div>

<script>
    function toggleTareaCompletada(tareaId, completada) {
        fetch(`/tareas/${tareaId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    completada
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error al actualizar la tarea');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la tarea');
            });
    }

    // Auto-submit form on Enter key
    document.addEventListener('DOMContentLoaded', function() {
        const formTareaRapida = document.getElementById('form-tarea-rapida');
        const inputTareaRapida = document.getElementById('input-tarea-rapida');

        if (inputTareaRapida && formTareaRapida) {
            inputTareaRapida.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this.value.trim() !== '') {
                        formTareaRapida.submit();
                    }
                }
            });
        }
    });
</script>
@endsection