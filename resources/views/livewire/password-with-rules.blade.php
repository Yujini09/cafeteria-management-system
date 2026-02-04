@php
    $ruleLabels = \App\Support\PasswordRules::ruleLabels();
    $ruleResults = $this->rulesResult;
    $isAdmin = $variant === 'admin';
    $inputClass = 'w-full rounded-admin border px-admin-input py-2.5 pr-12 text-sm transition-colors duration-200 focus:outline-none focus:ring-2 '
        . ($isAdmin
            ? 'border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20'
            : 'border-green-400 focus:border-orange-500 focus:ring-orange-500/20');
    $labelClass = $isAdmin ? 'text-admin-neutral-700' : 'text-green-700 font-medium';
    $eyeClass = $isAdmin ? 'text-admin-neutral-500 hover:text-admin-neutral-700' : 'text-green-600 hover:text-orange-500';
    $validIconClass = $isAdmin ? 'text-emerald-600' : 'text-green-600';
    $validTextClass = $validIconClass;
    $invalidTextClass = 'text-red-600';
    $minLength = \App\Support\PasswordRules::MIN_LENGTH;
@endphp
<div
    class="space-y-1"
    x-data="{
        show: false,
        value: @js($password),
        rules: {
            min: v => (v || '').length >= {{ $minLength }},
            number: v => /[0-9]/.test(v || ''),
            special: v => /[^A-Za-z0-9]/.test(v || ''),
            uppercase: v => /[A-Z]/.test(v || ''),
        },
        passes(key) {
            return this.rules[key] ? this.rules[key](this.value || '') : false;
        },
    }"
    wire:key="password-with-rules-{{ $name }}"
>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium {{ $labelClass }}">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <div class="relative">
        <input
            type="password"
            :type="show ? 'text' : 'password'"
            wire:model.live="password"
            @input="value = $event.target.value"
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            class="{{ $inputClass }}"
            autocomplete="{{ $name === 'password' ? 'new-password' : 'current-password' }}"
        >
        <button
            type="button"
            @click="show = !show"
            class="absolute right-3 top-1/2 -translate-y-1/2 {{ $eyeClass }} focus:outline-none transition-colors duration-200"
            :aria-label="show ? 'Hide password' : 'Show password'"
        >
            <x-ui.eye-toggle class="w-5 h-5" />
        </button>
    </div>
    @if($showRequirements)
        <ul class="mt-2 space-y-1.5 text-xs transition-opacity duration-200" role="list">
            @foreach($ruleLabels as $key => $label)
                @php $passed = $ruleResults[$key] ?? false; @endphp
                <li class="flex items-center gap-2 transition-colors duration-150">
                    <span
                        :class="passes('{{ $key }}') ? '{{ $validIconClass }}' : 'text-red-500'"
                        aria-hidden="true"
                    >
                        <span x-show="passes('{{ $key }}')" x-cloak class="{{ $validIconClass }}">&#10003;</span>
                        <span x-show="!passes('{{ $key }}')" x-cloak class="text-red-500">&#10007;</span>
                    </span>
                    <span
                        :class="passes('{{ $key }}') ? '{{ $validTextClass }}' : '{{ $invalidTextClass }}'"
                    >{{ $label }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
