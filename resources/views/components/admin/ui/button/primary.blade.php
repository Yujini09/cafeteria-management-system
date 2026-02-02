{{-- Admin primary button: main actions (submit, create, save). Consistent hover/focus/disabled. --}}
<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-admin font-semibold text-sm text-white
            bg-admin-primary hover:bg-admin-primary-hover
            focus:outline-none focus:ring-2 focus:ring-admin-primary focus:ring-offset-2
            active:opacity-90
            disabled:opacity-50 disabled:cursor-not-allowed
            transition-all duration-admin ease-out shadow-admin'
    ]) }}
>
    {{ $slot }}
</button>
