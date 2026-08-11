@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination"
         class="flex flex-col-reverse items-center justify-between gap-3 sm:flex-row">
        {{-- Summary --}}
        <p class="text-xs text-slate-500">
            Showing
            <span class="font-semibold text-slate-700">{{ $paginator->firstItem() ?? 0 }}</span>
            –
            <span class="font-semibold text-slate-700">{{ $paginator->lastItem() ?? 0 }}</span>
            of
            <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
        </p>

        <ul class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="grid h-9 w-9 place-items-center rounded-lg text-sm text-slate-300" aria-disabled="true">←</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="grid h-9 w-9 place-items-center rounded-lg text-sm font-semibold text-slate-600 transition hover:bg-violet-50 hover:text-violet-700"
                       rel="prev" aria-label="Previous">←</a>
                </li>
            @endif

            {{-- Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="grid h-9 min-w-[2.25rem] place-items-center rounded-lg px-2 text-xs text-slate-400">{{ $element }}</span>
                    </li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="grid h-9 min-w-[2.25rem] place-items-center rounded-lg bg-gradient-to-br from-violet-500 to-pink-500 px-2 text-sm font-bold text-white shadow-sm"
                                      aria-current="page">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="grid h-9 min-w-[2.25rem] place-items-center rounded-lg px-2 text-sm font-semibold text-slate-600 transition hover:bg-violet-50 hover:text-violet-700">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="grid h-9 w-9 place-items-center rounded-lg text-sm font-semibold text-slate-600 transition hover:bg-violet-50 hover:text-violet-700"
                       rel="next" aria-label="Next">→</a>
                </li>
            @else
                <li>
                    <span class="grid h-9 w-9 place-items-center rounded-lg text-sm text-slate-300" aria-disabled="true">→</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
