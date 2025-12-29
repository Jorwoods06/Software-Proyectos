@extends('layouts.app')

@section('title', 'Gestión de Roles y Permisos')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3 text-primary">Gestión de Roles y Permisos — {{ $user->nombre }}</h2>

    <form action="{{ route('users.updateRolesAndPermissions', $user->id) }}" method="POST" id="formRolesPermisos">
        @csrf
        @method('PUT')

        {{-- ===== ROLES ===== --}}
        <div class="mb-4">
            <h5 class="mb-2">Roles asignados</h5>
            <div class="d-flex flex-wrap gap-3">
                @foreach($roles as $rol)
                    <div class="form-check">
                        <input type="checkbox"
                               name="roles[]"
                               value="{{ $rol->id }}"
                               id="rol-{{ $rol->id }}"
                               class="form-check-input"
                               {{ $user->roles->contains($rol->id) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="rol-{{ $rol->id }}">{{ $rol->nombre }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===== PERMISOS ===== --}}
        <div class="card shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Permisos Directos y Heredados</h5>
                    <input type="search" id="buscarPermiso" class="form-control form-control-sm w-25" placeholder="Buscar permiso...">
                </div>

                <div id="listaPermisos" class="row g-2">
                    @foreach($permisos as $permiso)
                        @php
                            $permisoDirecto = $user->permisosDirectos->firstWhere('id', $permiso->id);
                            $permisoHeredado = $user->roles->flatMap->permisos->firstWhere('id', $permiso->id);
                            $checked = $permisoDirecto || $permisoHeredado ? 'checked' : '';
                            $tipo = $permisoDirecto->pivot->tipo ?? '';
                        @endphp

                        <div class="col-12 permiso-item" data-nombre="{{ strtolower($permiso->nombre) }}">
                            <div class="d-flex align-items-center justify-content-between p-2 border rounded
                                        @if($permisoHeredado && !$permisoDirecto) bg-light @endif">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox"
                                           class="form-check-input permiso-checkbox"
                                           name="permisos[]"
                                           value="{{ $permiso->id }}"
                                           id="permiso-{{ $permiso->id }}"
                                           {{ $checked }}>
                                    <label for="permiso-{{ $permiso->id }}" class="mb-0 small fw-medium">
                                        {{ $permiso->nombre }}
                                        @if($permisoHeredado && !$permisoDirecto)
                                            <span class="badge bg-info ms-1">Heredado</span>
                                        @endif
                                    </label>
                                </div>

                                <div>
                                    <select name="tipos[{{ $permiso->id }}]"
                                            class="form-select form-select-sm tipo-select"
                                            aria-label="Tipo permiso {{ $permiso->nombre }}">
                                        <option value="">Seleccione</option>
                                        <option value="allow" {{ $tipo === 'allow' ? 'selected' : '' }}>Permitir</option>
                                        <option value="deny" {{ $tipo === 'deny' ? 'selected' : '' }}>Denegar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-save me-1"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // --- UX: Activar / desactivar visualmente selects según el checkbox ---
    document.querySelectorAll('.permiso-item').forEach(item => {
        const checkbox = item.querySelector('.permiso-checkbox');
        const select = item.querySelector('.tipo-select');

        // Si el permiso es heredado, bloqueamos cambios directos (solo visual)
        const esHeredado = item.querySelector('.badge.bg-info') !== null;
        if (esHeredado && !checkbox.checked) {
            checkbox.checked = true;
        }

        // Estado inicial: si es heredado, marcamos visualmente
        actualizarSelectVisual(checkbox, select, esHeredado);

        // Cambio de checkbox
        checkbox.addEventListener('change', () => {
            actualizarSelectVisual(checkbox, select, esHeredado);
        });

        // Si el usuario toca el select y el checkbox no está marcado, lo marcamos automáticamente
        select.addEventListener('focus', () => {
            if (!checkbox.checked && !esHeredado) {
                checkbox.checked = true;
                actualizarSelectVisual(checkbox, select, esHeredado);
            }
        });
    });

    function actualizarSelectVisual(cb, select, esHeredado) {
        if (esHeredado) {
            select.disabled = true;
            select.classList.add('bg-light', 'text-muted');
        } else if (cb.checked) {
            select.disabled = false;
            select.classList.remove('bg-light', 'text-muted');
            if (!select.value) select.value = 'allow';
        } else {
            select.disabled = true;
            select.classList.add('bg-light', 'text-muted');
            select.value = '';
        }
    }

    // --- Buscador de permisos ---
    const inputBuscar = document.getElementById('buscarPermiso');
    let debounce;
    inputBuscar.addEventListener('input', function() {
        clearTimeout(debounce);
        const query = this.value.toLowerCase();
        debounce = setTimeout(() => {
            document.querySelectorAll('.permiso-item').forEach(item => {
                const nombre = item.dataset.nombre;
                item.style.display = nombre.includes(query) ? '' : 'none';
            });
        }, 200);
    });
});
</script>

<style>
.permiso-item .form-check-input { width: 1rem; height: 1rem; }
.permiso-item label { margin-bottom: 0; }
.small { font-size: .85rem; }
.fw-medium { font-weight: 600; }
.bg-light .badge { background-color: #0dcaf0 !important; }
.table-hover tbody tr:hover { background-color: #f1f7ff !important; }
</style>
@endsection
