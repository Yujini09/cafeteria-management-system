{{-- Admin secondary button: cancel, close, neutral actions. Consistent with primary for hierarchy. --}}
<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-admin font-semibold text-sm
            bg-admin-secondary text-admin-secondary-text
            hover:bg-admin-secondary-hover
            focus:outline-none focus:ring-2 focus:ring-admin-neutral-300 focus:ring-offset-2
            disabled:opacity-50 disabled:cursor-not-allowed
            transition-all duration-admin ease-out border border-admin-neutral-200'
    ]) }}
>
    {{ $slot }}
</button>
