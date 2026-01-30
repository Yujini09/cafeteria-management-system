{{-- Admin select: label, spacing, error, helper. --}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'placeholder' => null,
    'helper' => null,
    'required' => false,
    'searchable' => false,
    'searchPlaceholder' => 'Search options...',
])

@php
$hasError = $errors->has($name);
$ariaLabel = $attributes->get('aria-label') ?? (!$label ? ucwords(str_replace('_', ' ', $name)) : null);
$isSearchable = filter_var($searchable, FILTER_VALIDATE_BOOLEAN);
$selectClass = 'w-full rounded-admin border px-admin-input py-2.5 text-sm
    transition-colors duration-admin focus:outline-none focus:ring-2
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
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
        data-admin-select="true"
        @if($isSearchable) data-searchable="true" data-search-placeholder="{{ $searchPlaceholder }}" @endif
        {{ $attributes->merge(['class' => trim($selectClass . ' admin-select')]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ (string) $value === (string) old($name, $attributes->get('value')) ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
        {{ $slot }}
    </select>
    @if($helper && !$hasError)
        <p class="text-xs text-admin-neutral-500">{{ $helper }}</p>
    @endif
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
