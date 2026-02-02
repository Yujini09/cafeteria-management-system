{{-- Admin danger button: delete, destructive actions. Clear visual distinction. --}}
<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-admin font-semibold text-sm text-white
            bg-admin-danger hover:bg-admin-danger-hover
            focus:outline-none focus:ring-2 focus:ring-admin-danger focus:ring-offset-2
            disabled:opacity-50 disabled:cursor-not-allowed
            transition-all duration-admin ease-out'
    ]) }}
>
    {{ $slot }}
</button>
