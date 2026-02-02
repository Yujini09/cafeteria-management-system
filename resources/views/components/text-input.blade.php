@props(['disabled' => false, 'type' => 'text', 'autocomplete' => null])

@php
$autoComplete = $autocomplete ?? match($type) {
    'email' => 'email',
    'tel', 'phone' => 'tel',
    'url' => 'url',
    'password' => 'current-password',
    default => null,
};
@endphp

<input type="{{ $type }}" {{ $autoComplete ? "autocomplete=\"$autoComplete\"" : '' }} @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition duration-200']) }}>
