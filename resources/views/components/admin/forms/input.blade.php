{{-- Admin text input: label, spacing, error, helper. Use for text/email/number. --}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'helper' => null,
    'required' => false,
    'value' => null,
    'id' => null,
    'autocomplete' => null,
])

@php
$hasError = $errors->has($name);
$inputClass = 'w-full rounded-admin border px-admin-input py-2.5 text-sm
    transition-colors duration-admin focus:outline-none focus:ring-2
    ' . ($hasError
        ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
        : 'border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20');

// Auto-generate sensible autocomplete if not provided
$autoComplete = $autocomplete ?? match($type) {
    'email' => 'email',
    'tel', 'phone' => 'tel',
    'url' => 'url',
    default => null,
};
@endphp
<div class="space-y-1">
    @if($label)
        <label for="{{ $id ?? $name }}" class="block text-sm font-medium text-admin-neutral-700">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $autoComplete ? "autocomplete=\"$autoComplete\"" : '' }}
        {{ $attributes->merge(['class' => $inputClass]) }}
    >
    @if($helper && !$hasError)
        <p class="text-xs text-admin-neutral-500">{{ $helper }}</p>
    @endif
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
