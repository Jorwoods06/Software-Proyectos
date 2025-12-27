@extends('dashboard')

@section('title', 'Trazabilidad - ' . $proyecto->nombre)

@section('content')
<div class="container">
    <h2>Historial de Trazabilidad</h2>
    <p><strong>Proyecto:</strong> {{ $proyecto->nombre }}</p>
    <hr>

    @if($trazas->isEmpty())
        <div class="alert alert-info">No hay registros de trazabilidad.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trazas as $t)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($t->fecha)->format('d/m/Y H:i') }}</td>
                        <td>{{ $t->usuario->nombre ?? 'Usuario No encontrado' }}</td>
                        <td>{{ $t->accion }}</td>
                        <td><pre class="mb-0">{{ $t->detalle }}</pre></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('proyectos.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
