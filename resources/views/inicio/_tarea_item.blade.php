<div class="tarea-item-dashboard">
    <input type="checkbox" 
           class="tarea-checkbox-dash" 
           {{ $tarea->estaCompletada() ? 'checked' : '' }}
           onchange="toggleTareaCompletada({{ $tarea->id }}, this.checked)">
    
    <div class="tarea-info-dash">
        <div class="tarea-nombre-dash {{ $tarea->estaCompletada() ? 'completada' : '' }}">
            {{ $tarea->nombre }}
        </div>
        
        {{-- Sub-task / Proyecto --}}
        @if($tarea->actividad && $tarea->actividad->proyecto)
            <div class="tarea-subtask">
                <i class="bi bi-folder"></i>
                <span>{{ $tarea->actividad->proyecto->nombre }}</span>
            </div>
        @endif

        <div class="tarea-meta-dash">
            {{-- Badge Vencida si aplica (debe aparecer primero) --}}
            @if(isset($vencida) && $vencida)
                <span class="badge-dashboard badge-vencida">Vencida</span>
            @endif

            {{-- Prioridad --}}
            <span class="badge-dashboard badge-prioridad-{{ $tarea->prioridad ?? 'media' }}">
                {{ ucfirst($tarea->prioridad ?? 'media') }}
            </span>

            {{-- Estado --}}
            <span class="badge-dashboard badge-estado-{{ $tarea->estado }}">
                @if($tarea->estado === 'completado')
                    Completado
                @elseif($tarea->estado === 'en_progreso')
                    En Progreso
                @else
                    Pendiente
                @endif
            </span>

            {{-- Fecha --}}
            @if($tarea->fecha_fin)
                <span class="tarea-proyecto">
                    <i class="bi bi-calendar3"></i>
                    {{ \Carbon\Carbon::parse($tarea->fecha_fin)->format('d M Y') }}
                </span>
            @endif
        </div>
    </div>
</div>

