@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-3">
        @if ($paginator->onFirstPage())
            <span class="btn-secondary !py-2 !text-xs opacity-40 pointer-events-none">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-secondary !py-2 !text-xs">← Prev</a>
        @endif

        <span class="text-xs text-slate-500">Page {{ $paginator->currentPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-secondary !py-2 !text-xs">Next →</a>
        @else
            <span class="btn-secondary !py-2 !text-xs opacity-40 pointer-events-none">Next →</span>
        @endif
    </nav>
@endif
