@php
    $buttonBaseClass = 'inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg px-2.5 text-[11px] font-semibold sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs';
    $disabledClass = $buttonBaseClass . ' border border-admin-neutral-200 bg-admin-neutral-50 text-admin-neutral-400 cursor-not-allowed';
    $defaultClass = $buttonBaseClass . ' border border-admin-neutral-200 bg-white text-admin-neutral-700 transition-colors duration-150 hover:bg-admin-neutral-50';
    $activeClass = $buttonBaseClass . ' border border-admin-primary bg-admin-primary text-white shadow-sm';
    $buildCompactPageItems = function (int $totalPages, int $currentPage): array {
        if ($totalPages <= 5) {
            return range(1, $totalPages);
        }

        if ($currentPage <= 3) {
            return [1, 2, 3, '...', $totalPages - 1, $totalPages];
        }

        if ($currentPage >= $totalPages - 2) {
            return [1, 2, '...', $totalPages - 2, $totalPages - 1, $totalPages];
        }

        return [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $totalPages];
    };
    $mobilePageItems = $buildCompactPageItems($paginator->lastPage(), $paginator->currentPage());
@endphp

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

        <div class="flex w-full sm:w-auto sm:justify-end">
            <nav role="navigation" aria-label="Pagination" class="flex w-full flex-wrap items-center justify-center gap-1 sm:hidden">
                @if ($paginator->onFirstPage())
                    <span class="{{ $disabledClass }}">&lt;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       wire:navigate
                       class="{{ $defaultClass }}"
                       rel="prev">
                        &lt;
                    </a>
                @endif

                @foreach ($mobilePageItems as $item)
                    @if ($item === '...')
                        <span class="inline-flex h-8 min-w-[32px] items-center justify-center px-2.5 text-[11px] font-semibold text-admin-neutral-400">
                            {{ $item }}
                        </span>
                    @elseif ($item === $paginator->currentPage())
                        <span class="{{ $activeClass }}" aria-current="page">
                            {{ $item }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($item) }}"
                           wire:navigate
                           class="{{ $defaultClass }}"
                           aria-label="Go to page {{ $item }}">
                            {{ $item }}
                        </a>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       wire:navigate
                       class="{{ $defaultClass }}"
                       rel="next">
                        &gt;
                    </a>
                @else
                    <span class="{{ $disabledClass }}">&gt;</span>
                @endif
            </nav>

            <nav role="navigation" aria-label="Pagination" class="hidden w-full flex-wrap items-center justify-center gap-1 sm:flex sm:w-auto sm:justify-end">
                @if ($paginator->onFirstPage())
                    <span class="{{ $disabledClass }}">
                        &lt;
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       wire:navigate
                       class="{{ $defaultClass }}"
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
                                <span class="{{ $activeClass }}" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   wire:navigate
                                   class="{{ $defaultClass }}"
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
                       class="{{ $defaultClass }}"
                       rel="next">
                        &gt;
                    </a>
                @else
                    <span class="{{ $disabledClass }}">
                        &gt;
                    </span>
                @endif
            </nav>
        </div>
    </div>
@endif
