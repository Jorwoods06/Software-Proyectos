@extends('layouts.app')

@section('title', 'Tareas de la Actividad')

@section('content')
<div class="container">
    <h2 class="mb-4">Tareas de la actividad</h2>

    {{-- Crear tarea --}}
    @can('crear tareas')
    <form action="{{ route('tareas.store', [$proyectoId, $actividadId]) }}" method="POST" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="titulo" class="form-control" placeholder="Título" required>
            </div>
            <div class="col-md-4">
                <input type="date" name="fecha_entrega" class="form-control">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100">Agregar Tarea</button>
            </div>
        </div>
    </form>
    @endcan

    {{-- Listado --}}
    <div class="list-group">
        @foreach($tareas as $tarea)
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="mb-1">{{ $tarea->titulo }}</h5>
                    <p class="mb-1 text-muted">{{ $tarea->detalle }}</p>
                    <small>Estado: {{ ucfirst($tarea->estado) }} | Entrega: {{ $tarea->fecha_entrega }}</small>

                    {{-- Evidencias --}}
                    <div class="mt-2">
                        <strong>Evidencias:</strong>
                        <ul>
                            @foreach($tarea->evidencias as $ev)
                                <li>
                                    <a href="{{ Storage::url($ev->archivo) }}" target="_blank">{{ basename($ev->archivo) }}</a>
                                    @can('eliminar evidencias')
                                    <form action="{{ route('evidencias.destroy', [$proyectoId,$actividadId,$tarea->id,$ev->id]) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                    @endcan
                                </li>
                            @endforeach
                        </ul>

                        @can('subir evidencias')
                        <form action="{{ route('evidencias.store', [$proyectoId,$actividadId,$tarea->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="archivo" required>
                            <button class="btn btn-sm btn-secondary mt-1">Subir</button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $tareas->links() }}
    </div>
</div>
@endsection
