@if(isset($paginator) && $paginator->hasPages())
    <nav aria-label="Paginación">
        <ul class="pagination justify-content-center mb-0">
            {{-- Botón Anterior --}}
            @if($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" aria-disabled="true" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            @endif

            {{-- Enlaces de páginas --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
            @endphp
            
            @if($startPage > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if($startPage > 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif
            
            @for($page = $startPage; $page <= $endPage; $page++)
                @if($page == $currentPage)
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor
            
            @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
            @endif

            {{-- Botón Siguiente --}}
            @if($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" aria-disabled="true" aria-label="Siguiente">
                        <span aria-hidden="true">&raquo;</span>
                    </span>
                </li>
            @endif
        </ul>
        
        {{-- Información de resultados --}}
        <div class="text-center mt-2">
            <small class="text-muted">
                Mostrando {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
            </small>
        </div>
    </nav>
@endif

