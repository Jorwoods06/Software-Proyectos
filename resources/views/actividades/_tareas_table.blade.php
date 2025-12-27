@php
    $auth_user = \App\Models\User::with('roles')->find(session('user_id'));
    $puedeGestionar = $proyecto->puedeGestionarActividadesYTareas($auth_user);
@endphp

{{-- Tabla de Tareas --}}
@if($tareas->count() > 0)
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width: 30px;">
                    <input type="checkbox" class="form-check-input" id="select-all-{{ $actividad->id }}" onchange="toggleAllTasks({{ $actividad->id }}, this.checked)">
                </th>
                <th style="width: 40px;">#</th>
                <th>NOMBRE DE TAREA</th>
                <th>USUARIOS ASIGNADOS</th>
                <th>FECHA LÍMITE</th>
                <th>PRIORIDAD</th>
                <th>ESTADO</th>
                <th style="width: 100px;">ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tareas as $index => $tarea)
            <tr>
                <td>
                    <input type="checkbox" 
                           class="form-check-input tarea-checkbox" 
                           data-tarea-id="{{ $tarea->id }}"
                           {{ $tarea->estaCompletada() ? 'checked' : '' }}
                           onchange="toggleTareaCompletada({{ $tarea->id }}, this.checked)">
                </td>
                <td>{{ $tareas->firstItem() + $index }}</td>
                <td>
                    <span class="{{ $tarea->estaCompletada() ? 'text-decoration-line-through text-muted' : 'fw-medium' }}">
                        {{ $tarea->nombre }}
                    </span>
                </td>
                <td>
                    @if($tarea->usuariosAsignados->count() > 0)
                        <div class="d-flex align-items-center" style="gap: -8px;">
                            @foreach($tarea->usuariosAsignados->take(3) as $usuario)
                                <div class="avatar-small" 
                                     style="background: {{ $tarea->color_prioridad ?? '#0D6EFD' }}; margin-left: {{ $loop->first ? '0' : '-8px' }};"
                                     title="{{ $usuario->nombre }}"
                                     data-bs-toggle="tooltip">
                                    {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                                </div>
                            @endforeach
                            @if($tarea->usuariosAsignados->count() > 3)
                                <span class="badge bg-secondary ms-1">+{{ $tarea->usuariosAsignados->count() - 3 }}</span>
                            @endif
                        </div>
                    @else
                        <span class="text-muted">Sin asignar</span>
                    @endif
                </td>
                <td>
                    @php
                        $auth_user = \App\Models\User::with('roles')->find(session('user_id'));
                        $usuarioAsignadoFecha = $auth_user && (
                            $tarea->user_id === $auth_user->id || 
                            $tarea->usuariosAsignados->contains('id', $auth_user->id)
                        );
                    @endphp
                    @if($usuarioAsignadoFecha)
                        <div class="fecha-clickable" onclick="mostrarDatePicker({{ $tarea->id }})">
                            @if($tarea->fecha_fin)
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center {{ $tarea->estaVencida() ? 'fecha-vencida' : 'text-muted' }}">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <span>{{ \Carbon\Carbon::parse($tarea->fecha_fin)->format('d/m/Y') }}</span>
                                        <i class="bi bi-pencil ms-1" style="font-size: 0.75rem;"></i>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted"><i class="bi bi-calendar-plus me-1"></i>Agregar fecha</span>
                            @endif
                        </div>
                        <div class="date-picker-inline" id="date-picker-{{ $tarea->id }}" style="display: none;">
                            <div class="date-picker-content">
                                <input type="date" 
                                       id="fecha-fin-{{ $tarea->id }}" 
                                       value="{{ $tarea->fecha_fin ? $tarea->fecha_fin->format('Y-m-d') : '' }}" 
                                       class="form-control form-control-sm mb-2">
                                <input type="time" 
                                       id="hora-fin-{{ $tarea->id }}" 
                                       value="{{ $tarea->fecha_fin ? $tarea->fecha_fin->format('H:i') : '' }}" 
                                       class="form-control form-control-sm mb-2">
                                <div class="d-flex gap-1">
                                    <button type="button" 
                                            class="btn btn-sm btn-primary" 
                                            onclick="guardarFecha({{ $tarea->id }})">
                                        <i class="bi bi-check"></i> Guardar
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-secondary" 
                                            onclick="ocultarDatePicker({{ $tarea->id }})">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        @if($tarea->fecha_fin)
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center {{ $tarea->estaVencida() ? 'fecha-vencida' : 'text-muted' }}">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <span>{{ \Carbon\Carbon::parse($tarea->fecha_fin)->format('d M Y') }}</span>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    @endif
                </td>
                <td>
                    <span class="badge badge-prioridad-{{ $tarea->prioridad ?? 'media' }}">
                        {{ ucfirst($tarea->prioridad ?? 'media') }}
                    </span>
                </td>
                <td>
                    @php
                        $auth_user = \App\Models\User::with('roles')->find(session('user_id'));
                        $usuarioAsignado = $auth_user && (
                            $tarea->user_id === $auth_user->id || 
                            $tarea->usuariosAsignados->contains('id', $auth_user->id)
                        );
                    @endphp
                    @if($usuarioAsignado)
                        <div class="dropdown-estado-inline" data-tarea-id="{{ $tarea->id }}">
                            <div class="badge-clickable badge badge-estado-{{ $tarea->estado }}" onclick="toggleDropdownEstado({{ $tarea->id }})">
                                @if($tarea->estado === 'completado')
                                    Completado
                                @elseif($tarea->estado === 'en_progreso')
                                    En Progreso
                                @else
                                    Pendiente
                                @endif
                                <i class="bi bi-chevron-down ms-1"></i>
                            </div>
                            <div class="dropdown-menu-estado" id="dropdown-estado-{{ $tarea->id }}">
                                <div class="dropdown-item-estado {{ $tarea->estado === 'en_progreso' ? 'active' : '' }}" 
                                     onclick="cambiarEstado({{ $tarea->id }}, 'en_progreso')">
                                    <span class="badge-estado-item badge-estado-progreso">En Progreso</span>
                                </div>
                                <div class="dropdown-item-estado {{ $tarea->estado === 'pendiente' ? 'active' : '' }}" 
                                     onclick="cambiarEstado({{ $tarea->id }}, 'pendiente')">
                                    <span class="badge-estado-item bg-secondary">Pendiente</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <span class="badge badge-estado-{{ $tarea->estado }}">
                            @if($tarea->estado === 'completado')
                                Completado
                            @elseif($tarea->estado === 'en_progreso')
                                En Progreso
                            @else
                                Pendiente
                            @endif
                        </span>
                    @endif
                </td>
                <td>
                    @php
                        $auth_user = \App\Models\User::with('roles')->find(session('user_id'));
                        $puedeGestionarTarea = $proyecto->puedeGestionarActividadesYTareas($auth_user);
                    @endphp
                    <div class="d-flex gap-1">
                        <button type="button" 
                                class="btn btn-sm btn-outline-info" 
                                onclick="abrirPanelEvidenciasTarea({{ $tarea->id }})"
                                title="Evidencias">
                            <i class="bi bi-file-earmark-text"></i>
                        </button>
                        @if($puedeGestionarTarea)
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary" 
                                    onclick="editarTarea({{ $tarea->id }}, {{ $actividad->id }})"
                                    title="Editar"
                                    data-tarea-id="{{ $tarea->id }}"
                                    data-tarea-nombre="{{ $tarea->nombre }}"
                                    data-tarea-descripcion="{{ $tarea->descripcion ?? '' }}"
                                    data-tarea-fecha-inicio="{{ $tarea->fecha_inicio ? $tarea->fecha_inicio->format('Y-m-d') : '' }}"
                                    data-tarea-fecha-fin="{{ $tarea->fecha_fin ? $tarea->fecha_fin->format('Y-m-d') : '' }}"
                                    data-tarea-hora-fin="{{ $tarea->fecha_fin ? $tarea->fecha_fin->format('H:i') : '' }}"
                                    data-tarea-prioridad="{{ $tarea->prioridad ?? 'media' }}"
                                    data-tarea-estado="{{ $tarea->estado }}"
                                    data-tarea-usuarios="{{ $tarea->usuariosAsignados->pluck('id')->toJson() }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('tareas.destroy', $tarea->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirmarEliminarTarea(event, '{{ $tarea->nombre }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div id="pagination-{{ $actividad->id }}" class="mt-3">
    @include('partials.pagination', ['paginator' => $tareas])
</div>
@else
<div class="py-2 text-center text-muted">
    No hay tareas en esta fase
</div>
@endif

