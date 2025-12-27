<div class="tarea-item-dashboard">
    <input type="checkbox" 
           class="tarea-checkbox-dash" 
           {{ $tarea->estaCompletada() ? 'checked' : '' }}
           onchange="toggleTareaCompletada({{ $tarea->id }}, this.checked)">
    
    <div class="tarea-info-dash">
        <div class="tarea-nombre-dash {{ $tarea->estaCompletada() ? 'completada' : '' }}">
            {{ $tarea->nombre }}
        </div>
        
        <div class="tarea-meta-dash">
            {{-- Proyecto --}}
            @if($tarea->actividad && $tarea->actividad->proyecto)
                <div class="tarea-proyecto">
                    <i class="bi bi-folder"></i>
                    <span>{{ $tarea->actividad->proyecto->nombre }}</span>
                </div>
            @elseif($tarea->esIndependiente())
                <div class="tarea-proyecto">
                    <i class="bi bi-star"></i>
                    <span>Tarea Independiente</span>
                </div>
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
                <div class="tarea-proyecto">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ \Carbon\Carbon::parse($tarea->fecha_fin)->format('d M Y') }}</span>
                    @if($tarea->estaVencida())
                        <span class="badge-dashboard badge-vencida ms-2">Vencida</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

