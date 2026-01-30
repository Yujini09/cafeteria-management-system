{{-- Admin icon-only button: consistent size and hover for table actions, toolbars. --}}
@props([
    'variant' => 'secondary', // secondary | primary | danger
])

@php
$variantClasses = [
    'secondary' => 'bg-admin-neutral-100 text-admin-neutral-700 hover:bg-admin-neutral-200 focus:ring-admin-neutral-300',
    'primary' => 'bg-admin-primary text-white hover:bg-admin-primary-hover focus:ring-admin-primary',
    'danger' => 'bg-admin-danger-light text-admin-danger hover:bg-red-100 focus:ring-admin-danger',
];
$classes = $variantClasses[$variant] ?? $variantClasses['secondary'];
@endphp
<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center justify-center w-9 h-9 rounded-admin font-medium
            focus:outline-none focus:ring-2 focus:ring-offset-2
            disabled:opacity-50 disabled:cursor-not-allowed
            transition-all duration-admin ' . $classes
    ]) }}
    title="{{ $attributes->get('title', '') }}"
>
    {{ $slot }}
</button>
