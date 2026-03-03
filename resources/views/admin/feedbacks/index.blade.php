@extends('layouts.sidebar')
@section('page-title', 'Feedback Management')

@section('content')
@php
    $totalFeedbacks = $feedbacks->total();
    $visibleFeedbacks = $visibleCount ?? 0;
    $hiddenFeedbacks = $hiddenCount ?? max($totalFeedbacks - $visibleFeedbacks, 0);
@endphp

<style>
.modern-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    border: 1px solid var(--neutral-200);
    overflow: hidden;
    transition: all 0.25s ease;
    position: relative;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
}

.page-header {
    flex-wrap: wrap;
}

.feedback-filter-panel {
    background: var(--neutral-50);
    padding: 1.25rem;
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    margin-bottom: 1.5rem;
}

.feedback-table th,
.feedback-table td {
    text-align: left;
}

.feedback-message-preview {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    line-height: 1.5;
    max-height: 3em;
    word-break: break-word;
}

.feedback-rating-stars {
    display: inline-flex;
    align-items: center;
    gap: 0.125rem;
}

.feedback-search-empty {
    display: none;
}

.feedback-search-empty.is-visible {
    display: block;
}

.feedback-row.is-hidden-by-search {
    display: none;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-actions {
        width: 100%;
    }
}
</style>

<div class="modern-card admin-page-shell p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0">
    <div class="page-header items-start">
        <div class="header-content">
            <div class="header-icon">
                <x-admin.ui.icon name="fa-comments" style="fas" class="text-white w-6 h-6" />
            </div>
            <div class="header-text">
                <h1 class="header-title">Customer Feedback</h1>
                <p class="header-subtitle">Review submitted feedback and control what appears on the homepage.</p>
            </div>
        </div>

        <div class="header-actions w-full md:w-auto flex flex-col items-end gap-3">
            <div class="relative w-full sm:w-64 md:w-80">
                <input type="text"
                       inputmode="search"
                       autocomplete="off"
                       id="feedbackSearchInput"
                       placeholder="Search feedback..."
                       class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                       oninput="filterFeedbackTable(this.value)"
                       aria-label="Search feedback">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-admin-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <button id="feedbackClearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-400 hover:text-admin-neutral-600" style="display: none;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
            <x-admin.ui.icon name="fa-comments" size="xs" />
            Total Feedback: {{ $totalFeedbacks }}
        </span>
    </div>

    <div class="feedback-filter-panel">
        <form method="GET" action="{{ route('admin.feedbacks.index') }}" class="flex flex-col gap-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <label for="visibility" class="text-sm font-semibold text-admin-neutral-700">Filter by Visibility</label>
                <div class="w-full sm:w-72">
                    <select name="visibility" id="visibility" onchange="this.form.submit()" class="admin-select w-full" data-admin-select="true">
                        <option value="" {{ ($visibility ?? '') === '' ? 'selected' : '' }}>All Feedback</option>
                        <option value="visible" {{ ($visibility ?? '') === 'visible' ? 'selected' : '' }}>Visible on Homepage ({{ $visibleFeedbacks }})</option>
                        <option value="hidden" {{ ($visibility ?? '') === 'hidden' ? 'selected' : '' }}>Hidden from Homepage ({{ $hiddenFeedbacks }})</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    @if($feedbacks->count() > 0)
        <div id="feedbackTableHost">
            <div id="feedbackTableScroll" data-table-scroll class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
                <table class="modern-table feedback-table table-fixed w-full">
                    <colgroup>
                        <col class="w-36">
                        <col class="w-44">
                        <col class="w-36">
                        <col class="w-auto">
                        <col class="w-44">
                        <col class="w-32">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rating</th>
                            <th class="px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Homepage</th>
                            <th class="px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="feedbackTableBody" class="divide-y divide-gray-200">
                        @foreach($feedbacks as $feedback)
                            @php
                                $rating = (int) ($feedback->rating ?? 5);
                                $searchValue = \Illuminate\Support\Str::lower(trim(($feedback->name ?? '') . ' ' . ($feedback->message ?? '')));
                                $viewModalName = 'feedback-view-' . $feedback->id;
                            @endphp
                            <tr class="feedback-row hover:bg-admin-neutral-50/80 transition-colors duration-admin" data-feedback-row data-search="{{ $searchValue }}">
                                <td class="px-4 py-4 align-top">
                                    <div class="text-sm font-medium text-admin-neutral-900">{{ $feedback->created_at->format('M d, Y') }}</div>
                                    <div class="mt-1 text-xs text-admin-neutral-400">{{ $feedback->created_at->format('g:i A') }}</div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="text-sm font-semibold text-admin-neutral-900">{{ $feedback->name }}</div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-center gap-2">
                                        <div class="feedback-rating-stars" aria-label="Rated {{ $rating }} out of 5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <x-admin.ui.icon
                                                    name="fa-star"
                                                    style="fas"
                                                    size="xs"
                                                    class="{{ $i <= $rating ? 'text-amber-400' : 'text-admin-neutral-300' }}"
                                                />
                                            @endfor
                                        </div>
                                        <span class="text-xs font-semibold text-admin-neutral-500">{{ number_format($rating, 1) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <p class="feedback-message-preview text-sm text-admin-neutral-700" title="{{ $feedback->message }}">{{ $feedback->message }}</p>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    @if($feedback->is_visible)
                                        <span class="inline-flex items-center gap-2 rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-green-700">
                                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                            Visible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                                            <span class="h-2 w-2 rounded-full bg-admin-neutral-400"></span>
                                            Hidden
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-admin.ui.button.icon
                                            type="button"
                                            variant="secondary"
                                            title="View feedback"
                                            onclick="window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: '{{ $viewModalName }}' }))"
                                            class="text-admin-primary hover:bg-admin-primary-light hover:text-admin-primary"
                                        >
                                            <x-admin.ui.icon name="fa-envelope-open-text" size="sm" />
                                        </x-admin.ui.button.icon>

                                        <form action="{{ route('admin.feedbacks.toggle', $feedback->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <x-admin.ui.button.icon
                                                type="submit"
                                                variant="secondary"
                                                title="{{ $feedback->is_visible ? 'Hide from homepage' : 'Show on homepage' }}"
                                                class="{{ $feedback->is_visible ? 'text-admin-warning hover:bg-amber-50 hover:text-admin-warning' : 'text-admin-primary hover:bg-admin-primary-light hover:text-admin-primary' }}"
                                            >
                                                <x-admin.ui.icon name="{{ $feedback->is_visible ? 'fa-eye-slash' : 'fa-eye' }}" size="sm" />
                                            </x-admin.ui.button.icon>
                                        </form>
                                    </div>

                                    <x-admin.ui.modal name="{{ $viewModalName }}" title="Feedback Details" variant="info" maxWidth="lg" icon="fa-envelope-open-text">
                                        <div class="space-y-5">
                                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-500">Customer Name</p>
                                                    <p class="mt-1 text-sm font-semibold text-admin-neutral-900">{{ $feedback->name }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-500">Date Submitted</p>
                                                    <p class="mt-1 text-sm text-admin-neutral-700">{{ $feedback->created_at->format('F j, Y \\a\\t g:i A') }}</p>
                                                </div>
                                            </div>

                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-500">Rating</p>
                                                <div class="mt-2 flex items-center gap-3">
                                                    <div class="feedback-rating-stars" aria-label="Rated {{ $rating }} out of 5">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <x-admin.ui.icon
                                                                name="fa-star"
                                                                style="fas"
                                                                size="sm"
                                                                class="{{ $i <= $rating ? 'text-amber-400' : 'text-admin-neutral-300' }}"
                                                            />
                                                        @endfor
                                                    </div>
                                                    <span class="text-sm font-semibold text-admin-neutral-600">{{ number_format($rating, 1) }} / 5.0</span>
                                                </div>
                                            </div>

                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-500">Homepage Status</p>
                                                <div class="mt-2">
                                                    @if($feedback->is_visible)
                                                        <span class="inline-flex items-center gap-2 rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-green-700">
                                                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                                            Visible on Homepage
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                                                            <span class="h-2 w-2 rounded-full bg-admin-neutral-400"></span>
                                                            Hidden from Homepage
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-500">Customer Review</p>
                                                <div class="mt-2 rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 px-4 py-4 text-sm leading-relaxed text-admin-neutral-700 whitespace-pre-wrap">{{ $feedback->message }}</div>
                                            </div>
                                        </div>
                                        <x-slot name="footer">
                                            <x-admin.ui.button.secondary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: '{{ $viewModalName }}' }))">
                                                Close
                                            </x-admin.ui.button.secondary>
                                        </x-slot>
                                    </x-admin.ui.modal>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="feedbackSearchEmpty" class="feedback-search-empty mt-4 rounded-admin border border-dashed border-admin-neutral-300 bg-admin-neutral-50 px-6 py-10 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-admin-neutral-100 text-admin-neutral-400">
                    <x-admin.ui.icon name="fa-magnifying-glass" size="sm" />
                </div>
                <h3 class="text-base font-semibold text-admin-neutral-900">No matching feedback</h3>
                <p class="mt-1 text-sm text-admin-neutral-500">Try a different keyword or clear the search.</p>
            </div>
        </div>
    @else
        <div class="rounded-admin border border-admin-neutral-200 bg-white px-6 py-16 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-admin-neutral-100 text-admin-neutral-400">
                <x-admin.ui.icon name="fa-comments" size="lg" />
            </div>
            <h3 class="text-lg font-semibold text-admin-neutral-900">No feedback found</h3>
            <p class="mt-1 text-sm text-admin-neutral-500">There is no feedback available for the selected filter yet.</p>
        </div>
    @endif

    @if($feedbacks->hasPages())
        <div class="mt-6">
            {{ $feedbacks->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script>
    function filterFeedbackTable(query) {
        const normalized = (query || '').trim().toLowerCase();
        const rows = document.querySelectorAll('[data-feedback-row]');
        const clearButton = document.getElementById('feedbackClearSearch');
        const emptyState = document.getElementById('feedbackSearchEmpty');
        let visibleRows = 0;

        rows.forEach((row) => {
            const haystack = row.dataset.search || '';
            const matches = normalized === '' || haystack.includes(normalized);

            row.classList.toggle('is-hidden-by-search', !matches);

            if (matches) {
                visibleRows += 1;
            }
        });

        if (clearButton) {
            clearButton.style.display = normalized === '' ? 'none' : 'block';
        }

        if (emptyState) {
            emptyState.classList.toggle('is-visible', rows.length > 0 && visibleRows === 0);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('feedbackSearchInput');
        const clearButton = document.getElementById('feedbackClearSearch');

        if (clearButton && searchInput) {
            clearButton.addEventListener('click', function () {
                searchInput.value = '';
                filterFeedbackTable('');
                searchInput.focus();
            });
        }

        if (searchInput) {
            filterFeedbackTable(searchInput.value);
        }
    });
</script>
@endsection
