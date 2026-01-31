@php
    $ruleLabels = \App\Support\PasswordRules::ruleLabels();
    $ruleKeys = array_keys($ruleLabels); // fixed order: min, number, special, uppercase
    $isAdmin = $variant === 'admin';
    $inputClass = 'w-full rounded-admin border px-admin-input py-2.5 pr-12 text-sm transition-colors duration-200 focus:outline-none focus:ring-2 '
        . ($isAdmin
            ? 'border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20'
            : 'border-green-400 focus:border-orange-500 focus:ring-orange-500/20');
    $labelClass = $isAdmin ? 'text-admin-neutral-700' : 'text-green-700 font-medium';
    $eyeClass = $isAdmin ? 'text-admin-neutral-500 hover:text-admin-neutral-700' : 'text-green-600 hover:text-orange-500';
    $rulesJson = json_encode($ruleLabels);
    $ruleKeysJson = json_encode($ruleKeys);
@endphp
<div
    class="space-y-1"
    x-data="passwordWithRules({{ $rulesJson }}, {{ $ruleKeysJson }})"
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
            name="{{ $name }}"
            id="{{ $name }}"
            x-model="password"
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
            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
            </svg>
        </button>
    </div>
    @if($showRequirements)
        <ul class="mt-2 space-y-1.5 text-xs transition-opacity duration-200" role="list">
            <template x-for="key in ruleKeys" :key="key">
                <li class="flex items-center gap-2 transition-colors duration-150">
                    <span x-show="passed(key)" class="{{ $isAdmin ? 'text-emerald-600' : 'text-green-600' }}" aria-hidden="true">✔</span>
                    <span x-show="!passed(key)" x-cloak class="text-red-500" aria-hidden="true">✖</span>
                    <span :class="passed(key) ? '{{ $isAdmin ? 'text-admin-neutral-600' : 'text-green-700' }}' : 'text-admin-neutral-500'" x-text="ruleLabels[key]"></span>
                </li>
            </template>
        </ul>
    @endif
    {{-- No Laravel validation message text here; errors are shown only via red ✖ on failed rules --}}
</div>
