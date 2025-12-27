@extends('dashboard')

@section('title','Usuarios')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Usuarios</h2>
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Nuevo Usuario</a>
        <a href="{{ route('users.duplicateForm') }}" class="btn btn-success">Gestionar Roles & Permisos</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Proceso</th>
            <th>Roles</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $u)
        <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->nombre }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->departamento_nombre }}</td>
            
            <td>
                @foreach($u->roles as $r)
                    <span class="badge bg-secondary">{{ $r->nombre }}</span>
                @endforeach
            </td>
            <td>{{ ucfirst($u->estado) }}</td>
            <td>
                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-sm btn-warning">Editar</a>
                
                <a href="{{ route('users.manage', $u->id) }}" class="btn btn-sm btn-info">
                    Roles & Permisos
                </a>

                <form action="{{ route('users.destroy',$u->id) }}" method="POST" style="display:inline"
                    onsubmit="return confirm('Eliminar usuario?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $users->links() }}
@endsection
