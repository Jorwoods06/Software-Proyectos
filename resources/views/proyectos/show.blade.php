@extends('dashboard')

@section('title', 'Detalle Proyecto')

@section('content')
<div class="container">
    <h2>{{ $proyecto->nombre }}</h2>
    <p>{{ $proyecto->descripcion }}</p>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Responsable:</strong> {{ $proyecto->responsable->name ?? '—' }}</li>
        <li class="list-group-item"><strong>Inicio:</strong> {{ $proyecto->fecha_inicio }}</li>
        <li class="list-group-item"><strong>Fin:</strong> {{ $proyecto->fecha_fin }}</li>
    </ul>

    @can('invitar usuarios proyecto')
    <h4>Invitar Colaborador</h4>
    <form action="{{ route('proyectos.invitar', $proyecto->id) }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <select name="user_id" class="form-select" required>
                    <option value="">Seleccione usuario</option>
                    @foreach($usuarios as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->departamento->nombre ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="rol_proyecto" class="form-control" placeholder="Rol en el proyecto" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Invitar</button>
            </div>
        </div>
    </form>
    @endcan

    <h4>Colaboradores</h4>
    <table class="table table-sm">
        <thead>
            <tr><th>Usuario</th><th>Rol</th></tr>
        </thead>
        <tbody>
            @foreach($proyecto->colaboradores as $colaborador)
                <tr>
                    <td>{{ $colaborador->name }}</td>
                    <td>{{ $colaborador->pivot->rol_proyecto }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('proyects.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
