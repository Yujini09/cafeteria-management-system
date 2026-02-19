{{--
  Unified admin modal: confirmation, warning, error, info.
  Overlay with blur, centered, ESC/click-outside, body scroll lock, smooth transitions.
  Usage: wrap in x-data with show; use x-admin.ui.modal with :show, variant, title, and slots.
  Or use Alpine with x-modelable so parent controls open state.
--}}
@props([
    'name' => 'admin-modal',
    'variant' => 'confirmation', // confirmation | warning | error | info
    'title' => null,
    'maxWidth' => 'md',
    'icon' => null, // optional Font Awesome icon name (e.g. fa-clock-rotate-left)
    'iconStyle' => 'fas', // fas | far | fal | fab
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
$iconBg = match($variant) {
    'warning' => 'bg-admin-warning-light',
    'error' => 'bg-admin-danger-light',
    'info' => 'bg-blue-100',
    default => 'bg-admin-primary-light',
};
$iconColor = match($variant) {
    'warning' => 'text-admin-warning',
    'error' => 'text-admin-danger',
    'info' => 'text-blue-600',
    default => 'text-admin-primary',
};
@endphp
<template x-teleport="body">
<div
    x-data="{ show: false, locked: false }"
    x-init="
        $watch('show', v => {
            if (v) document.body.classList.add('overflow-hidden');
            else document.body.classList.remove('overflow-hidden');
            if (!v) locked = false;
            window.dispatchEvent(
                new CustomEvent('admin-modal-visibility', {
                    detail: { name: '{{ $name }}', open: v }
                })
            );
        });
    "
    @keydown.escape.window="if (show && !locked) show = false"
    x-on:admin-modal-lock.window="if ($event.detail && $event.detail.name === '{{ $name }}') locked = Boolean($event.detail.locked)"
    x-on:open-admin-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-admin-modal.window="if ($event.detail === '{{ $name }}' && !locked) show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[220] flex items-center justify-center p-4"
    style="display: none;"
>
    {{-- Overlay with blur; click to close --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="if (!locked) show = false"
        class="absolute inset-0 bg-admin-neutral-900/50 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    {{-- Panel: prevent click from closing --}}
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
        class="relative w-full {{ $maxWidthClass }} bg-white rounded-admin-lg shadow-admin-modal border border-admin-neutral-200 overflow-hidden"
    >
        @if($title)
            <div class="flex items-center gap-3 px-6 py-4 border-b border-admin-neutral-100">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-admin {{ $iconBg }} {{ $iconColor }}">
                    @if($icon)
                        <x-admin.ui.icon :name="$icon" :style="$iconStyle" size="sm" />
                    @else
                        @if($variant === 'error')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        @elseif($variant === 'warning')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        @elseif($variant === 'info')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    @endif
                </span>
                <h2 id="modal-title-{{ $name }}" class="text-lg font-semibold text-admin-neutral-900">{{ $title }}</h2>
            </div>
        @endif
        <div class="px-6 py-4">
            {{ $slot }}
        </div>
        @if(isset($footer))
            <div class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-admin-neutral-100 bg-admin-neutral-50">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
</template>
