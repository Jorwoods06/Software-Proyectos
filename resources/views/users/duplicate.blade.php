@extends('dashboard')

@section('title','Gestión de Roles y Permisos')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">
        <h2 class="mb-0">Gestión de Roles & Permisos</h2>
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#rolesPermissionsModal">
            <i class="fas fa-key"></i> Gestionar Roles & Permisos Base
        </button>
    </div>

    <!-- Modal para gestionar roles y permisos -->
    <div class="modal fade" id="rolesPermissionsModal" tabindex="-1" aria-labelledby="rolesPermissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="rolesPermissionsModalLabel">Gestión de Roles y Permisos Base</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario para crear nuevo rol -->
                    <form id="createRoleForm" action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" name="nombre" class="form-control" placeholder="Nuevo nombre de rol" required>
                            <button class="btn btn-primary" type="submit">Crear Rol</button>
                        </div>
                    </form>

                    <!-- Gestión de permisos de todos los roles -->
                    <div class="accordion" id="rolesAccordion">
                        @foreach($roles as $rol)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $rol->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $rol->id }}" aria-expanded="false" aria-controls="collapse{{ $rol->id }}">
                                    {{ $rol->nombre }}
                                </button>
                            </h2>
                            <div id="collapse{{ $rol->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $rol->id }}" data-bs-parent="#rolesAccordion">
                                <div class="accordion-body">
                                    <form action="{{ route('roles.permissions.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="role_id" value="{{ $rol->id }}">
                                        <div class="row">
                                            @foreach($permisos as $permiso)
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                                                        {{ $rol->permisos->contains($permiso->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $permiso->nombre }}</label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="submit" class="btn btn-success mt-2">Guardar Permisos</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Columna izquierda: Roles/Permisos --}}
       <div class="col-md-6">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            Perfiles de la compañía
        </div>
        <div class="card-body p-2">
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-hover table-sm" id="rolesTable">
                    <thead class="table-light" style="font-size: 0.85rem;">
                        <tr>
                            <th>Nombre</th>
                            <th>Permisos asociados</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.85rem;">
                        @foreach($roles as $rol)
                        <tr>
                            <td><strong>{{ $rol->nombre }}</strong></td>
                            <td>
                                @foreach($rol->permisos as $p)
                                <span class="badge bg-secondary">{{ $p->nombre }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#rolesTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [5, 10, 20, 50],
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ roles",
            info: "Mostrando _START_ a _END_ de _TOTAL_ roles",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            zeroRecords: "No se encontraron roles"
        },
        columnDefs: [
            { orderable: false, targets: 1 } // Opcional: no ordenar columna de permisos
        ]
    });
});
</script>
@endsection


        {{-- Columna derecha: Usuarios --}}
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    Usuarios
                </div>
                <div class="card-body">
                    <form action="{{ route('users.duplicateRolesAndPermissions') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="from_user_id" class="form-label">Usuario Origen</label>
                                <input type="text" id="filterFromUser" class="form-control mb-2" placeholder="Buscar usuario origen...">
                                <select name="from_user_id" id="from_user_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->nombre }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="to_user_id" class="form-label">Usuario Destino</label>
                                <input type="text" id="filterToUser" class="form-control mb-2" placeholder="Buscar usuario destino...">
                                <select name="to_user_id" id="to_user_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->nombre }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Duplicar Roles & Permisos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla resumen usuarios y roles --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Usuarios y Roles
        </div>
        <div class="card-body">
            <input type="text" id="filterUsers" class="form-control mb-3" placeholder="Filtrar tabla de usuarios...">
            <table class="table table-striped table-hover" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Permisos Directos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->nombre }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @foreach($u->roles as $r)
                            <span class="badge bg-primary">{{ $r->nombre }}</span>
                            @endforeach
                        </td>
                        <td>
                            @foreach($u->permisosDirectos as $p)
                            <span class="badge bg-warning text-dark">{{ $p->nombre }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Div para mensajes de sesión --}}
<div id="session-messages"
     @if(session('success')) data-success="{{ session('success') }}" @endif
     @if(session('error')) data-error="{{ session('error') }}" @endif>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Mensajes SweetAlert ---
    const messages = document.getElementById('session-messages');

    if (messages) {
        const success = messages.dataset.success;
        const error = messages.dataset.error;

        if (success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: success,
                timer: 2500,
                showConfirmButton: false
            });
        }

        if (error) {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: error,
                timer: 2500,
                showConfirmButton: false
            });
        }
    }

    // ======= Filtrado de selects dinámico =======
    function enableSearch(inputId, selectId) {
        const input = document.getElementById(inputId);
        const select = document.getElementById(selectId);
        const originalOptions = Array.from(select.options);

        input.addEventListener("keyup", function() {
            const filter = input.value.toLowerCase();
            select.innerHTML = "";
            const defaultOption = originalOptions[0].cloneNode(true);
            select.appendChild(defaultOption);

            originalOptions.slice(1).forEach(option => {
                if(option.text.toLowerCase().includes(filter)){
                    select.appendChild(option.cloneNode(true));
                }
            });
        });
    }

    enableSearch("filterFromUser", "from_user_id");
    enableSearch("filterToUser", "to_user_id");

    // Filtro de tabla de usuarios
    document.getElementById('filterUsers').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#usersTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });

});
</script>
@endsection
