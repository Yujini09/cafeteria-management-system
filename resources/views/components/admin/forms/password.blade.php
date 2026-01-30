{{-- Admin password input with eye toggle. showRequirements only for create/reset password forms. --}}
@props([
    'name' => 'password',
    'label' => 'Password',
    'showRequirements' => false,
    'helper' => null,
    'required' => true,
])

@php
$hasError = $errors->has($name);
$inputClass = 'w-full rounded-admin border px-admin-input py-2.5 pr-12 text-sm
    transition-colors duration-admin focus:outline-none focus:ring-2
    ' . ($hasError
        ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
        : 'border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20');
@endphp
<div class="space-y-1" x-data="{ show: false }">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-admin-neutral-700">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => $inputClass]) }}
        >
        <button
            type="button"
            @click="show = !show"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-neutral-700 focus:outline-none"
            :aria-label="show ? 'Hide password' : 'Show password'"
        >
            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
            </svg>
        </button>
    </div>
    @if($showRequirements)
        <p class="text-xs text-admin-neutral-500">At least 8 characters.</p>
    @endif
    @if($helper && !$hasError && !$showRequirements)
        <p class="text-xs text-admin-neutral-500">{{ $helper }}</p>
    @endif
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
