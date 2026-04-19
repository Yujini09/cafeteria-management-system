@php
    $isAdmin = $variant === 'admin';
    $inputClass = 'w-full rounded-admin border py-2.5 pl-10 pr-12 text-sm transition-colors duration-200 focus:outline-none focus:ring-2 '
        . ($isAdmin
            ? 'border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 focus:border-admin-primary focus:ring-admin-primary/20'
            : 'border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 focus:border-admin-primary focus:ring-admin-primary/20');
    $labelClass = $isAdmin ? 'text-admin-neutral-700' : 'text-green-700 font-medium';
    $eyeClass = $isAdmin ? 'text-admin-neutral-500 hover:text-admin-neutral-700' : 'text-green-600 hover:text-orange-500';
    $lockIconClass = 'text-admin-neutral-500';
    $minLength = \App\Support\PasswordRules::MIN_LENGTH;
@endphp
<div
    class="space-y-2"
    x-data="{
        show: false,
        value: @js($password),
        minRule: v => (v || '').length >= {{ $minLength }},
        numberRule: v => /[0-9]/.test(v || ''),
        strength() {
            const password = this.value || '';
            if (!password.length) {
                return null;
            }

            const hasMin = this.minRule(password);
            const hasNumber = this.numberRule(password);
            if (!hasMin || !hasNumber) {
                return {
                    label: 'Weak',
                    message: 'Weak password. Use at least 8 characters and at least 1 number.',
                    className: 'text-red-600',
                };
            }

            let score = 0;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            if (password.length >= 12) score++;

            if (score >= 3) {
                return {
                    label: 'Strong',
                    message: 'Strong password.',
                    className: 'text-green-600',
                };
            }

            return {
                label: 'Medium',
                message: 'Medium password.',
                className: 'text-amber-600',
            };
        },
        strengthMessage() {
            const state = this.strength();
            return state ? state.message : '';
        },
        strengthClass() {
            const state = this.strength();
            return state ? state.className : '';
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
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 {{ $lockIconClass }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </span>
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
        <p
            x-show="value && value.length > 0"
            x-cloak
            class="text-xs"
            :class="strengthClass()"
            role="status"
            x-text="strengthMessage()"
        >
        </p>
    @endif
</div>
