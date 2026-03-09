@php
    $compactRuleLabels = \App\Support\PasswordRules::compactRuleLabels();
    $isAdmin = $variant === 'admin';
    $inputClass = 'w-full rounded-admin border py-2.5 pl-10 pr-12 text-sm transition-colors duration-200 focus:outline-none focus:ring-2 '
        . ($isAdmin
            ? 'border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 focus:border-admin-primary focus:ring-admin-primary/20'
            : 'border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 focus:border-admin-primary focus:ring-admin-primary/20');
    $labelClass = $isAdmin ? 'text-admin-neutral-700' : 'text-green-700 font-medium';
    $eyeClass = $isAdmin ? 'text-admin-neutral-500 hover:text-admin-neutral-700' : 'text-green-600 hover:text-orange-500';
    $lockIconClass = 'text-admin-neutral-500';
    $inactiveBarClass = 'bg-admin-neutral-200';
    $weakBarClass = 'bg-red-400';
    $mediumBarClass = 'bg-amber-400';
    $goodBarClass = $isAdmin ? 'bg-admin-primary/70' : 'bg-emerald-500';
    $strongBarClass = $isAdmin ? 'bg-admin-primary' : 'bg-green-700';
    $inactivePillClass = 'border-admin-neutral-300 bg-white text-admin-neutral-400';
    $activePillClass = $isAdmin
        ? 'border-admin-primary/20 bg-admin-primary-light text-admin-primary'
        : 'border-green-300 bg-green-100 text-green-700';
    $minLength = \App\Support\PasswordRules::MIN_LENGTH;
@endphp
<div
    class="space-y-2"
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
        score() {
            return ['min', 'number', 'special', 'uppercase']
                .filter((key) => this.passes(key))
                .length;
        },
        barClass(index) {
            const score = this.score();

            if (index > score) {
                return '{{ $inactiveBarClass }}';
            }

            if (score <= 1) {
                return '{{ $weakBarClass }}';
            }

            if (score === 2) {
                return '{{ $mediumBarClass }}';
            }

            if (score === 3) {
                return '{{ $goodBarClass }}';
            }

            return '{{ $strongBarClass }}';
        },
        pillClass(key) {
            return this.passes(key) ? '{{ $activePillClass }}' : '{{ $inactivePillClass }}';
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
        <div class="space-y-2" role="group" aria-label="Password strength requirements">
            <div class="flex gap-1.5">
                @for($index = 1; $index <= 4; $index++)
                    <span
                        class="h-1.5 flex-1 rounded-full transition-colors duration-150"
                        :class="barClass({{ $index }})"
                    ></span>
                @endfor
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($compactRuleLabels as $key => $compactLabel)
                    <span
                        class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide transition-colors duration-150"
                        :class="pillClass('{{ $key }}')"
                    >
                        <span aria-hidden="true">
                            <span x-show="passes('{{ $key }}')" x-cloak>&#10003;</span>
                            <span x-show="!passes('{{ $key }}')" x-cloak>&#10007;</span>
                        </span>
                        <span>{{ $compactLabel }}</span>
                    </span>
                @endforeach
            </div>
        </div>
    @endif
</div>
