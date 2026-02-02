{{-- Admin textarea: label, spacing, error, helper. --}}
@props([
    'name',
    'label' => null,
    'rows' => 4,
    'helper' => null,
    'required' => false,
])

@php
$hasError = $errors->has($name);
$textareaClass = 'w-full rounded-admin border px-admin-input py-2.5 text-sm
    transition-colors duration-admin resize-y min-h-[6rem] focus:outline-none focus:ring-2
    ' . ($hasError
        ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
        : 'border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20');
@endphp
<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-admin-neutral-700">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => $textareaClass]) }}
    >{{ old($name, $attributes->get('value')) }}</textarea>
    @if($helper && !$hasError)
        <p class="text-xs text-admin-neutral-500">{{ $helper }}</p>
    @endif
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
