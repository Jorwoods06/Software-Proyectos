@extends('layouts.app')

@section('title', 'Listado de Proyectos')

@section('content')
<style>
    /* Estilos consistentes con el resto de la aplicación */
    body {
        background: #f5f5f5 !important;
    }

    body .container {
        background: #f5f5f5;
        max-width: 1400px;
        padding: 1.3rem;
    }

    .proyectos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.3rem;
    }

    .proyectos-title {
        font-size: 1.32rem;
        font-weight: 600;
        color: #212529;
        margin: 0;
        line-height: 1.3;
    }

    .proyectos-card {
        background: white;
        border-radius: 8px;
        padding: 1.1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: none;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
        padding: 0.75rem 0.5rem;
        background-color: #f8f9fa;
    }

    .table tbody td {
        font-size: 0.825rem;
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
        color: #212529;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .proyecto-nombre-link {
        color: #0D6EFD;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .proyecto-nombre-link:hover {
        color: #0B5ED7;
        text-decoration: underline;
    }

    .proyecto-descripcion {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
        position: relative;
        transition: all 0.3s ease;
    }

    .proyecto-descripcion.has-tooltip:hover {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        max-width: 400px;
        word-wrap: break-word;
        z-index: 10;
        background-color: #ffffff;
        padding: 0.5rem;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #dee2e6;
    }

    .proyecto-descripcion-truncada {
        display: inline-block;
        max-width: 100%;
    }

    .badge-estado {
        padding: 0.165rem 0.44rem;
        border-radius: 11px;
        font-size: 0.66rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        line-height: 1.2;
    }

    .badge-estado.activo {
        background: #D4EDDA;
        color: #0F5132;
    }

    .badge-estado.en_progreso {
        background: #FFE69C;
        color: #856404;
    }

    .badge-estado.pendiente {
        background: #E9ECEF;
        color: #6C757D;
    }

    .badge-estado.completado {
        background: #CFE2FF;
        color: #084298;
    }

    .badge-estado.cancelado {
        background: #F8D7DA;
        color: #721C24;
    }

    .btn-accion {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-accion i {
        font-size: 0.75rem;
    }

    .acciones-cell {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 2.64rem;
        margin-bottom: 0.88rem;
        opacity: 0.5;
    }

    .pagination-wrapper {
        margin-top: 1.3rem;
        display: flex;
        justify-content: center;
    }

    @media (min-width: 768px) {
        .table thead th {
            font-size: 0.8125rem;
            padding: 0.875rem 0.75rem;
        }

        .table tbody td {
            font-size: 0.875rem;
            padding: 0.875rem 0.75rem;
        }

        .proyecto-descripcion {
            max-width: 400px;
        }
    }

    @media (min-width: 1400px) and (max-width: 1444px) and (min-height: 600px) and (max-height: 700px) {
        body .container {
            padding: 1.4rem;
        }

        .proyectos-title {
            font-size: 1.4rem;
        }

        .proyectos-card {
            padding: 1.2rem;
        }

        .table thead th {
            font-size: 0.85rem;
        }

        .table tbody td {
            font-size: 0.9rem;
        }
    }
</style>

<div class="container">
    <div class="proyectos-header">
        <h2 class="proyectos-title">Proyectos</h2>
        @permiso('crear proyecto')
            <a href="{{ route('proyectos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Proyecto
            </a>
        @endpermiso
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

    <div class="proyectos-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proyectos as $proyecto)
                        <tr>
                            <td>
                                <a href="{{ route('actividades.index', $proyecto->id) }}" class="proyecto-nombre-link">
                                    {{ $proyecto->nombre }}
                                </a>
                            </td>
                            <td>
                                @php
                                    $descripcion = $proyecto->descripcion ?? '';
                                    $maxLength = 100;
                                    $descripcionTruncada = strlen($descripcion) > $maxLength 
                                        ? substr($descripcion, 0, $maxLength) . '...' 
                                        : $descripcion;
                                    $necesitaTooltip = strlen($descripcion) > $maxLength;
                                @endphp
                                <div class="proyecto-descripcion {{ $necesitaTooltip ? 'has-tooltip' : '' }}" 
                                     @if($necesitaTooltip) title="{{ $descripcion }}" @endif>
                                    <span class="proyecto-descripcion-truncada">{{ $descripcionTruncada }}</span>
                                    @if($necesitaTooltip)
                                        <span class="descripcion-completa" style="display: none;">{{ $descripcion }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{
                                    $proyecto->usuarios
                                             ->firstWhere('pivot.rol_proyecto', 'lider')
                                             ?->nombre
                                    ?? 'Sin asignar'
                                }}
                            </td>
                            <td>
                                <span class="badge-estado {{ strtolower($proyecto->estado) }}">
                                    {{ ucfirst($proyecto->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="acciones-cell">
                                    @permiso('editar proyecto')
                                        <a href="{{ route('proyectos.edit', $proyecto->id) }}" 
                                           class="btn btn-sm btn-warning btn-accion" 
                                           data-bs-toggle="tooltip" 
                                           title="Editar proyecto">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endpermiso
                                    @permiso('eliminar proyecto')
                                        <form action="{{ route('proyectos.destroy', $proyecto->id) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('¿Seguro que deseas eliminar este proyecto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger btn-accion" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Eliminar proyecto">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endpermiso
                                    @permiso('invitar usuarios a este proyecto', ['proyecto' => $proyecto])
                                        <button class="btn btn-sm btn-info btn-accion btn-invitar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalInvitar" 
                                                data-proyecto-id="{{ $proyecto->id }}" 
                                                title="Invitar usuarios a este proyecto">
                                            <i class="bi bi-person-plus"></i>
                                        </button>
                                    @endpermiso
                                    @if ($auth_user && validacionExtra($auth_user, ['proyecto' => $proyecto]))
                                        <a href="{{ route('proyectos.trazabilidad', $proyecto->id) }}" 
                                           class="btn btn-sm btn-secondary btn-accion" 
                                           data-bs-toggle="tooltip" 
                                           title="Ver trazabilidad del proyecto">
                                            <i class="bi bi-diagram-3"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="empty-state">
                                    <i class="bi bi-folder-x"></i>
                                    <p>No hay proyectos registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($proyectos->hasPages())
        <div class="pagination-wrapper">
            {{ $proyectos->links() }}
        </div>
    @endif
</div>

<!-- Modal Invitar Usuario -->
<div class="modal fade" id="modalInvitar" tabindex="-1" aria-labelledby="modalInvitarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="form-invitar" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInvitarLabel">
                        <i class="fas fa-user-plus"></i> Invitar usuarios al proyecto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="proyecto_id" id="proyecto_id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Información:</strong> Selecciona usuarios por departamento y asígnales roles y permisos específicos para el proyecto.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Usuarios a invitar</strong>
                        </label>
                        <div id="usuarios-container">
                            <!-- Fila inicial -->
                            <div class="user-row mb-3 p-3 border rounded bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-user"></i> Usuario
                                        </label>
                                        <select class="form-select user-select" name="usuarios[0][user_id]" required>
                                            <option value="">Seleccione un usuario</option>
                                        </select>
                                        <small class="text-muted">Usuarios agrupados por departamento</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">
                                            <i class="fas fa-user-tag"></i> Rol en el proyecto
                                        </label>
                                        <select class="form-select" name="usuarios[0][rol_proyecto]" required>
                                            <option value="">Seleccionar rol</option>
                                            <option value="colaborador">Colaborador</option>
                                            <option value="visor">Visor</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-key"></i> Permisos específicos
                                        </label>
                                        <select class="form-select permisos-select" name="usuarios[0][permisos][]" multiple>
                                            @foreach($permisosDisponibles as $permiso)
                                                <option value="{{ $permiso->id }}">{{ $permiso->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Mantén Ctrl para seleccionar múltiples</small>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-danger eliminar-usuario" title="Eliminar usuario">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Información adicional del usuario seleccionado -->
                                <div class="user-info mt-2" style="display: none;">
                                    <div class="alert alert-secondary mb-0">
                                        <small>
                                            <strong>Departamento:</strong> <span class="departamento-info">-</span> |
                                            <strong>Email:</strong> <span class="email-info">-</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="agregar-usuario">
                            <i class="fas fa-plus"></i> Agregar otro usuario
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Enviar invitaciones
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let contadorFilas = 1;
let departamentosData = [];

// Template para cada fila de usuario
function crearFilaUsuario() {
    return `
        <div class="user-row mb-3 p-3 border rounded bg-light">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Usuario
                    </label>
                    <select class="form-select user-select" name="usuarios[${contadorFilas}][user_id]" required>
                        <option value="">Seleccione un usuario</option>
                    </select>
                    <small class="text-muted">Usuarios agrupados por departamento</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-user-tag"></i> Rol en el proyecto
                    </label>
                    <select class="form-select" name="usuarios[${contadorFilas}][rol_proyecto]" required>
                        <option value="">Seleccionar rol</option>
                        <option value="colaborador">Colaborador</option>
                        <option value="visor">Visor</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-key"></i> Permisos específicos
                    </label>
                    <select class="form-select permisos-select" name="usuarios[${contadorFilas}][permisos][]" multiple>
                        @foreach($permisosDisponibles as $permiso)
                            <option value="{{ $permiso->id }}">{{ $permiso->nombre }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Mantén Ctrl para seleccionar múltiples</small>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger eliminar-usuario" title="Eliminar usuario">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <!-- Información adicional del usuario seleccionado -->
            <div class="user-info mt-2" style="display: none;">
                <div class="alert alert-secondary mb-0">
                    <small>
                        <strong>Departamento:</strong> <span class="departamento-info">-</span> |
                        <strong>Email:</strong> <span class="email-info">-</span>
                    </small>
                </div>
            </div>
        </div>
    `;
}

// Función para poblar select con usuarios agrupados por departamento
function poblarSelectUsuarios(selectElement) {
    selectElement.innerHTML = '<option value="">Seleccione un usuario</option>';
    
    departamentosData.forEach(departamento => {
        if (departamento.usuarios && departamento.usuarios.length > 0) {
            const optgroup = document.createElement('optgroup');
            optgroup.label = departamento.nombre;
            
            departamento.usuarios.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.id;
                option.textContent = `${usuario.nombre} (${usuario.email})`;
                option.dataset.departamento = departamento.nombre;
                option.dataset.email = usuario.email;
                optgroup.appendChild(option);
            });
            
            selectElement.appendChild(optgroup);
        }
    });
}

// Función para mostrar información del usuario seleccionado
function mostrarInfoUsuario(selectElement) {
    const userRow = selectElement.closest('.user-row');
    const userInfo = userRow.querySelector('.user-info');
    const departamentoInfo = userRow.querySelector('.departamento-info');
    const emailInfo = userRow.querySelector('.email-info');
    
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectedOption.value) {
        departamentoInfo.textContent = selectedOption.dataset.departamento || '-';
        emailInfo.textContent = selectedOption.dataset.email || '-';
        userInfo.style.display = 'block';
    } else {
        userInfo.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalInvitar');
    const form = document.getElementById('form-invitar');
    const usuariosContainer = document.getElementById('usuarios-container');
    const agregarUsuarioBtn = document.getElementById('agregar-usuario');
    const inputProyecto = document.getElementById('proyecto_id');

    // Agregar fila de usuario
    agregarUsuarioBtn.addEventListener('click', () => {
        usuariosContainer.insertAdjacentHTML('beforeend', crearFilaUsuario());
        const nuevoSelect = usuariosContainer.lastElementChild.querySelector('.user-select');
        poblarSelectUsuarios(nuevoSelect);
        
        // Agregar event listener para mostrar info del usuario
        nuevoSelect.addEventListener('change', () => mostrarInfoUsuario(nuevoSelect));
        
        contadorFilas++;
    });

    // Eliminar fila de usuario
    usuariosContainer.addEventListener('click', (e) => {
        if (e.target.closest('.eliminar-usuario')) {
            const userRows = usuariosContainer.querySelectorAll('.user-row');
            if (userRows.length > 1) {
                e.target.closest('.user-row').remove();
            } else {
                alert('Debe mantener al menos una fila para invitar usuarios.');
            }
        }
    });

    // Cargar departamentos y usuarios al abrir el modal
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        const proyectoId = button.getAttribute('data-proyecto-id');
        inputProyecto.value = proyectoId;
        
        // Mostrar loading
        const firstSelect = usuariosContainer.querySelector('.user-select');
        firstSelect.innerHTML = '<option value="">Cargando usuarios...</option>';
        
        // Cargar departamentos con usuarios
        fetch(`/proyectos/${proyectoId}/usuarios`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar usuarios');
                }
                return response.json();
            })
            .then(data => {
                departamentosData = data;
                
                // Poblar todos los selects existentes
                usuariosContainer.querySelectorAll('.user-select').forEach(select => {
                    poblarSelectUsuarios(select);
                });
                
                if (data.length === 0) {
                    firstSelect.innerHTML = '<option value="">No hay usuarios disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                firstSelect.innerHTML = '<option value="">Error al cargar usuarios</option>';
                alert('Error al cargar la lista de usuarios disponibles.');
            });
    });

    // Event listener para el primer select (ya existente)
    usuariosContainer.querySelector('.user-select').addEventListener('change', function() {
        mostrarInfoUsuario(this);
    });

    // Enviar formulario
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const proyectoId = inputProyecto.value;
        const url = `/proyectos/${proyectoId}/invitar`;
        const formData = new FormData(form);
        
        // Deshabilitar botón de envío
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Usuarios invitados correctamente');
                modal.querySelector('.btn-close').click();
                location.reload();
            } else {
                alert(data.message || 'Error al invitar usuarios');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        })
        .finally(() => {
            // Rehabilitar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Limpiar modal al cerrar
    modal.addEventListener('hidden.bs.modal', () => {
        form.reset();
        
        // Mantener solo la primera fila
        const userRows = usuariosContainer.querySelectorAll('.user-row');
        for (let i = 1; i < userRows.length; i++) {
            userRows[i].remove();
        }
        
        // Ocultar info de usuario
        usuariosContainer.querySelector('.user-info').style.display = 'none';
        
        // Reset contador
        contadorFilas = 1;
        departamentosData = [];
    });

    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Mostrar descripción completa al hacer hover
    document.querySelectorAll('.proyecto-descripcion.has-tooltip').forEach(function(element) {
        const descripcionTruncada = element.querySelector('.proyecto-descripcion-truncada');
        const descripcionCompleta = element.querySelector('.descripcion-completa');
        
        if (descripcionTruncada && descripcionCompleta) {
            element.addEventListener('mouseenter', function() {
                descripcionTruncada.style.display = 'none';
                descripcionCompleta.style.display = 'inline';
            });
            
            element.addEventListener('mouseleave', function() {
                descripcionTruncada.style.display = 'inline-block';
                descripcionCompleta.style.display = 'none';
            });
        }
    });
});
</script>