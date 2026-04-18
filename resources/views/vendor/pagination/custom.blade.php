@if ($paginator->hasPages())
<nav class="flex items-center justify-between" aria-label="Pagination">
    <p class="text-xs text-slate-600">
        Showing <span class="text-slate-400">{{ $paginator->firstItem() }}</span>–<span class="text-slate-400">{{ $paginator->lastItem() }}</span>
        of <span class="text-slate-400">{{ $paginator->total() }}</span> results
    </p>

    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-[var(--navy-800)] border border-white/5 text-slate-700 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="w-9 h-9 flex items-center justify-center rounded-lg bg-[var(--navy-800)] border border-white/8 text-slate-400 hover:border-white/20 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="w-9 h-9 flex items-center justify-center text-xs text-slate-600">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-[var(--gold)] text-[var(--navy-950)] text-xs font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="w-9 h-9 flex items-center justify-center rounded-lg bg-[var(--navy-800)] border border-white/8 text-slate-400 hover:border-white/20 hover:text-white text-xs transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="w-9 h-9 flex items-center justify-center rounded-lg bg-[var(--navy-800)] border border-white/8 text-slate-400 hover:border-white/20 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-[var(--navy-800)] border border-white/5 text-slate-700 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </div>
</nav>
@endif
