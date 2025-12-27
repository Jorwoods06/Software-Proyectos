@extends('layouts.app')

@section('title', 'Crear Proyecto')

@section('content')
<style>
    .form-card {
        background: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 2rem;
    }

    .colaboradores-preview {
        background-color: transparent;
        border-radius: 0.5rem;
        padding: 0.75rem;
        min-height: 60px;
    }

    .colaborador-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .colaborador-avatar {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: #ffffff;
        background: #3b82f6;
        flex-shrink: 0;
    }

    .colaborador-chip-content {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .colaborador-chip-label {
        font-size: 0.875rem;
        color: #111827;
        font-weight: 500;
        white-space: nowrap;
    }

    .colaborador-chip-role {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
            margin: 0 1rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .colaboradores-preview {
            padding: 0.5rem;
        }

        .colaborador-chip {
            padding: 0.4rem 0.6rem;
        }

        .colaborador-avatar {
            width: 28px;
            height: 28px;
        }
    }

    .colaborador-chip-remove {
        border: none;
        background: transparent;
        color: #9ca3af;
        padding: 0;
        margin-left: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: color 0.2s;
    }

    .colaborador-chip-remove:hover {
        color: #ef4444;
    }

    .colaborador-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }

    .colaborador-modal.open {
        display: flex;
    }

    .colaborador-modal-dialog {
        background: #ffffff;
        border-radius: 0.75rem;
        padding: 0.9rem 1rem 1rem 1rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.25);
    }

    .colaborador-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .colaborador-modal-title {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .colaborador-modal-close {
        border: none;
        background: transparent;
        font-size: 1.1rem;
        line-height: 1;
        color: #6b7280;
    }

    .colaborador-modal-close:hover {
        color: #111827;
    }

    .colaborador-modal-search input {
        font-size: 0.85rem;
    }

    .colaborador-modal-role-select {
        font-size: 0.8rem;
    }

    .colaborador-modal-list {
        max-height: 260px;
        overflow-y: auto;
        margin-top: 0.5rem;
    }

    .colaborador-modal-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.35rem 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.8rem;
    }

    .colaborador-modal-item:last-child {
        border-bottom: none;
    }

    .colaborador-modal-item-name {
        color: #111827;
    }

    .colaborador-modal-item-email {
        color: #6b7280;
        font-size: 0.75rem;
    }


    @media (min-width: 1920px) {
    .content {
        padding: 1.5rem 10rem;
    }
}
</style>

<h1 class="page-title">Nuevo Proyecto</h1>
<p class="page-subtitle">
    Complete la información a continuación para crear un nuevo espacio de trabajo y asignar recursos.
</p>

<form action="{{ route('proyectos.store') }}" method="POST" id="formCrearProyecto">
    @csrf

    {{-- Tarjeta: Información del proyecto --}}
    <div class="form-card mb-3">
        <div class="mb-3">
            <label class="form-label small fw-semibold">Nombre del Proyecto <span class="text-danger">*</span></label>
            <input
                type="text"
                name="nombre"
                class="form-control form-control-sm"
                placeholder="Ej: Rediseño del sitio web corporativo"
                value="{{ old('nombre') }}"
                required
            >
            @error('nombre')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-semibold">Descripción</label>
            <textarea
                name="descripcion"
                class="form-control form-control-sm"
                rows="3"
                placeholder="Describa los objetivos y el alcance del proyecto..."
            >{{ old('descripcion') }}</textarea>
        </div>

        <div class="row g-3 mb-1">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Fecha Inicio</label>
                <div class="position-relative">
                    <input type="date" name="fecha_inicio" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Fecha Fin</label>
                <div class="position-relative">
                    <input type="date" name="fecha_fin" class="form-control form-control-sm" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjeta: Colaboradores del proyecto --}}
    <div class="form-card mb-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start mb-3 gap-2">
            <div>
                <label class="form-label small fw-semibold mb-1">Colaboradores del Proyecto</label>
                <div class="text-muted small">
                    Gestione el equipo del proyecto, asigne roles y permisos.
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1" id="btnAgregarColaborador">
                <i class="bi bi-plus-lg"></i>
                <span>Agregar</span>
            </button>
        </div>

        <div id="colaboradores-preview" class="colaboradores-preview d-flex align-items-start flex-wrap">
            {{-- Chips de colaboradores seleccionados --}}
        </div>
        <div id="colaboradores-container" class="d-none">
            {{-- Los colaboradores se agregarán dinámicamente aquí (inputs y selects) --}}
        </div>
    </div>

    <div class="d-flex justify-content-end align-items-center gap-2">
        <a href="{{ route('proyectos.index') }}" class="btn btn-light btn-sm">
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary btn-sm px-4">
            Guardar Proyecto
        </button>
    </div>
</form>

{{-- Modal simple para buscar y agregar colaboradores --}}
<div id="colaboradorModal" class="colaborador-modal" aria-hidden="true">
    <div class="colaborador-modal-dialog">
        <div class="colaborador-modal-header">
            <div>
                <div class="colaborador-modal-title">Agregar colaborador</div>
                <div class="text-muted" style="font-size: 0.75rem;">Busque por nombre o correo electrónico.</div>
            </div>
            <button type="button" class="colaborador-modal-close" id="cerrarColaboradorModal" aria-label="Cerrar">&times;</button>
        </div>

        <div class="colaborador-modal-search mb-2">
            <input type="text" id="busquedaColaborador" class="form-control form-control-sm" placeholder="Escriba para buscar...">
        </div>

        <div class="mb-2">
            <select id="rolColaboradorSeleccionado" class="form-select form-select-sm colaborador-modal-role-select">
                <option value="colaborador">Colaborador</option>
                <option value="lider">Líder</option>
                <option value="visor">Visor</option>
            </select>
        </div>

        <div id="listaResultadosColaborador" class="colaborador-modal-list small">
            {{-- Resultados de búsqueda --}}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colaboradoresContainer = document.getElementById('colaboradores-container');
    const colaboradoresPreview = document.getElementById('colaboradores-preview');
    const btnAgregar = document.getElementById('btnAgregarColaborador');

    const modal = document.getElementById('colaboradorModal');
    const cerrarModalBtn = document.getElementById('cerrarColaboradorModal');
    const busquedaInput = document.getElementById('busquedaColaborador');
    const listaResultados = document.getElementById('listaResultadosColaborador');
    const rolSelect = document.getElementById('rolColaboradorSeleccionado');
    let colaboradorCount = 0;
    const colaboradoresAgregados = new Set(); // Para rastrear IDs de colaboradores ya agregados

    const departamentos = @json($departamentos ?? []);

    // Aplanar usuarios por departamento para facilitar la búsqueda
    const usuarios = [];
    departamentos.forEach(function (dept) {
        (dept.usuarios || []).forEach(function (user) {
            usuarios.push({
                id: user.id,
                nombre: user.nombre,
                email: user.email
            });
        });
    });

    function crearChipInicial(nombreCompleto) {
        if (!nombreCompleto) return '??';
        const partes = nombreCompleto.trim().split(' ');
        if (partes.length === 1) return partes[0].slice(0, 2).toUpperCase();
        return (partes[0][0] + partes[1][0]).toUpperCase();
    }

    function abrirModal() {
        modal.classList.add('open');
        busquedaInput.value = '';
        renderResultados('');
        setTimeout(function () { busquedaInput.focus(); }, 50);
    }

    function cerrarModal() {
        modal.classList.remove('open');
    }

    function agregarColaborador(user, rol) {
        // Verificar si el colaborador ya fue agregado
        if (colaboradoresAgregados.has(user.id)) {
            return; // No agregar duplicados
        }

        const index = colaboradorCount++;
        colaboradoresAgregados.add(user.id); // Marcar como agregado

        // Inputs ocultos que se enviarán al backend
        const wrapper = document.createElement('div');
        wrapper.className = 'd-none';
        wrapper.innerHTML = `
            <input type="hidden" name="colaboradores[${index}][user_id]" value="${user.id}">
            <input type="hidden" name="colaboradores[${index}][rol_proyecto]" value="${rol}">
        `;
        colaboradoresContainer.appendChild(wrapper);

        // Chip visual
        const chip = document.createElement('div');
        chip.className = 'colaborador-chip';
        chip.setAttribute('data-user-id', user.id);
        chip.innerHTML = `
            <div class="colaborador-avatar">${crearChipInicial(user.nombre)}</div>
            <div class="colaborador-chip-content">
                <span class="colaborador-chip-label">${user.nombre}</span>
                <span class="colaborador-chip-role">${rol.toUpperCase()}</span>
            </div>
            <button type="button" class="colaborador-chip-remove" aria-label="Quitar colaborador">
                <i class="bi bi-x-circle" style="font-size: 0.875rem;"></i>
            </button>
        `;
        colaboradoresPreview.appendChild(chip);

        chip.querySelector('.colaborador-chip-remove').addEventListener('click', function() {
            colaboradoresAgregados.delete(user.id); // Permitir agregarlo nuevamente
            chip.remove();
            wrapper.remove();
            renderResultados(busquedaInput.value); // Actualizar lista
        });
    }

    function renderResultados(filtro) {
        const termino = filtro.trim().toLowerCase();
        listaResultados.innerHTML = '';

        const filtrados = usuarios.filter(function (u) {
            // Excluir colaboradores ya agregados
            if (colaboradoresAgregados.has(u.id)) return false;
            
            if (!termino) return true;
            return (
                (u.nombre && u.nombre.toLowerCase().includes(termino)) ||
                (u.email && u.email.toLowerCase().includes(termino))
            );
        });

        if (filtrados.length === 0) {
            const vacio = document.createElement('div');
            vacio.className = 'text-muted small py-2';
            vacio.textContent = 'No se encontraron resultados.';
            listaResultados.appendChild(vacio);
            return;
        }

        filtrados.forEach(function (user) {
            const item = document.createElement('div');
            item.className = 'colaborador-modal-item';
            item.innerHTML = `
                <div class="me-2">
                    <div class="colaborador-modal-item-name">${user.nombre}</div>
                    <div class="colaborador-modal-item-email">${user.email}</div>
                </div>
                <button type="button" class="btn btn-primary btn-sm">Agregar</button>
            `;

            item.querySelector('button').addEventListener('click', function () {
                agregarColaborador(user, rolSelect.value || 'colaborador');
                // limpiar input, mantener modal abierto para seguir agregando
                busquedaInput.value = '';
                renderResultados('');
            });

            listaResultados.appendChild(item);
        });
    }

    // Eventos
    btnAgregar.addEventListener('click', abrirModal);
    cerrarModalBtn.addEventListener('click', cerrarModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            cerrarModal();
        }
    });

    busquedaInput.addEventListener('input', function () {
        renderResultados(busquedaInput.value);
    });
});
</script>
@endsection
