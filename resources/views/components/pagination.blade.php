@if ($paginator->hasPages())
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-admin-neutral-500">
            Showing
            <span class="font-semibold text-admin-neutral-700">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold text-admin-neutral-700">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-admin-neutral-700">{{ $paginator->total() }}</span>
            results
        </p>

        <nav role="navigation" aria-label="Pagination" class="inline-flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-neutral-200 bg-admin-neutral-50 text-xs font-semibold text-admin-neutral-400 cursor-not-allowed">
                    Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   wire:navigate
                   class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-neutral-200 bg-white text-xs font-semibold text-admin-neutral-700 hover:bg-admin-neutral-50 transition-colors duration-150"
                   rel="prev">
                    Prev
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-xs font-semibold text-admin-neutral-400">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-primary bg-admin-primary text-xs font-semibold text-white shadow-sm" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               wire:navigate
                               class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-neutral-200 bg-white text-xs font-semibold text-admin-neutral-700 hover:bg-admin-neutral-50 transition-colors duration-150"
                               aria-label="Go to page {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   wire:navigate
                   class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-neutral-200 bg-white text-xs font-semibold text-admin-neutral-700 hover:bg-admin-neutral-50 transition-colors duration-150"
                   rel="next">
                    Next
                </a>
            @else
                <span class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-neutral-200 bg-admin-neutral-50 text-xs font-semibold text-admin-neutral-400 cursor-not-allowed">
                    Next
                </span>
            @endif
        </nav>
    </div>
@endif
