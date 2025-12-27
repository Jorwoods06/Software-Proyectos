@extends('dashboard')

@section('title', 'Editar Proyecto')

@section('content')
<div class="container">
    <h2>Editar Proyecto</h2>
    <hr>

    <form action="{{ route('proyectos.update', $proyecto->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre"
                   class="form-control"
                   value="{{ old('nombre', $proyecto->nombre) }}"
                   required>
            @error('nombre') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $proyecto->descripcion) }}</textarea>
            @error('descripcion') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" name="fecha_inicio"
                       class="form-control"
                       value="{{ old('fecha_inicio', $proyecto->fecha_inicio) }}"
                       required>
                @error('fecha_inicio') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="col">
                <label class="form-label">Fecha Fin</label>
                <input type="date" name="fecha_fin"
                       class="form-control"
                       value="{{ old('fecha_fin', $proyecto->fecha_fin) }}"
                       required>
                @error('fecha_fin') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3" id="justificacion-wrapper" style="display:none;">
            <label class="form-label">Justificación del cambio de fechas</label>
            <textarea name="justificacion_fecha" class="form-control">{{ old('justificacion_fecha') }}</textarea>
        </div>

        <script>
            // Mostrar textarea solo si se modifican fechas
            const fechaInicio = "{{ $proyecto->fecha_inicio }}";
            const fechaFin    = "{{ $proyecto->fecha_fin }}";

            document.querySelector('input[name="fecha_inicio"]').addEventListener('change', toggleJustificacion);
            document.querySelector('input[name="fecha_fin"]').addEventListener('change', toggleJustificacion);

            function toggleJustificacion() {
                const f1 = document.querySelector('input[name="fecha_inicio"]').value;
                const f2 = document.querySelector('input[name="fecha_fin"]').value;
                document.getElementById('justificacion-wrapper').style.display =
                    (f1 !== fechaInicio || f2 !== fechaFin) ? 'block' : 'none';
            }
        </script>


        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('proyectos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@endsection

