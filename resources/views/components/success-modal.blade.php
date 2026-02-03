@props([
    'name',
    'title' => 'Success!',
    'maxWidth' => 'sm',
    'overlayClass' => 'bg-transparent',
])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '6xl' => 'sm:max-w-6xl',
    default => 'sm:max-w-md',
};
@endphp

<template x-teleport="body">
<div
    x-data="{ show: false, closeTimer: null }"
    x-init="
        $watch('show', v => {
            if (v) document.body.classList.add('overflow-hidden');
            else document.body.classList.remove('overflow-hidden');
            if (v) {
                if (closeTimer) clearTimeout(closeTimer);
                closeTimer = setTimeout(() => { show = false; }, 2000);
            } else if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }
        });
    "
    @keydown.escape.window="if (show) show = false"
    x-on:open-admin-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-admin-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="show = false"
        class="absolute inset-0 {{ $overlayClass }} backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    <div
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title-{{ $name }}"
        class="relative w-full {{ $maxWidthClass }} overflow-hidden rounded-admin-lg border border-admin-neutral-200 bg-white shadow-admin-modal"
    >
        <div class="flex items-start justify-between gap-4 border-b border-admin-neutral-100 bg-admin-neutral-50 px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-admin bg-admin-success-light text-admin-success">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <div>
                    <h2 id="modal-title-{{ $name }}" class="text-lg font-semibold text-admin-neutral-900">{{ $title }}</h2>
                    <p class="text-xs text-admin-neutral-600">Action completed successfully.</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-5 text-sm text-admin-neutral-700">
            @if($slot->isEmpty())
                <p>Action completed successfully.</p>
            @else
                {{ $slot }}
            @endif
        </div>
        @if(isset($footer))
            <div class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-admin-neutral-100 bg-admin-neutral-50">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
</template>
