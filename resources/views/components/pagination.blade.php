@if ($paginator->hasPages())
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-center text-xs leading-relaxed text-admin-neutral-500 sm:text-left">
            Showing
            <span class="font-semibold text-admin-neutral-700">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold text-admin-neutral-700">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-admin-neutral-700">{{ $paginator->total() }}</span>
            results
        </p>

        <nav role="navigation" aria-label="Pagination" class="flex w-full flex-wrap items-center justify-center gap-1 sm:w-auto sm:justify-end">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg border border-admin-neutral-200 bg-admin-neutral-50 px-2.5 text-[11px] font-semibold text-admin-neutral-400 cursor-not-allowed sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs">
                    &lt;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   wire:navigate
                   class="inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg border border-admin-neutral-200 bg-white px-2.5 text-[11px] font-semibold text-admin-neutral-700 transition-colors duration-150 hover:bg-admin-neutral-50 sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs"
                   rel="prev">
                    &lt;
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-8 min-w-[32px] items-center justify-center px-2.5 text-[11px] font-semibold text-admin-neutral-400 sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg border border-admin-primary bg-admin-primary px-2.5 text-[11px] font-semibold text-white shadow-sm sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               wire:navigate
                               class="inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg border border-admin-neutral-200 bg-white px-2.5 text-[11px] font-semibold text-admin-neutral-700 transition-colors duration-150 hover:bg-admin-neutral-50 sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs"
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
                   class="inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg border border-admin-neutral-200 bg-white px-2.5 text-[11px] font-semibold text-admin-neutral-700 transition-colors duration-150 hover:bg-admin-neutral-50 sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs"
                   rel="next">
                    &gt;
                </a>
            @else
                <span class="inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg border border-admin-neutral-200 bg-admin-neutral-50 px-2.5 text-[11px] font-semibold text-admin-neutral-400 cursor-not-allowed sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs">
                    &gt;
                </span>
            @endif
        </nav>
    </div>
@endif
