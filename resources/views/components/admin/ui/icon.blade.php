{{--
  Admin icon: single style and size. Uses Font Awesome to match sidebar.
  Usage: <x-admin.ui.icon name="user" /> or <x-admin.ui.icon name="fa-plus" class="text-admin-primary" />
  name: Font Awesome icon name without fa- prefix (e.g. user, plus, trash) or full class (fa-plus).
--}}
@props([
    'name' => '',
    'style' => 'fas', // fas | far | fal | fab
    'size' => 'default', // xs | sm | default | lg
])

@php
$rawIconClass = str_starts_with($name, 'fa-') ? $name : 'fa-' . $name;

// Font Awesome 6 compatibility for legacy icon names still used in templates.
$legacyIconMap = [
    'fa-file-alt' => 'fa-file-lines',
    'fa-trash-alt' => 'fa-trash-can',
    'fa-exclamation-triangle' => 'fa-triangle-exclamation',
];

$iconClass = $legacyIconMap[$rawIconClass] ?? $rawIconClass;

$normalizedStyle = strtolower((string) $style);
$styleClass = match ($normalizedStyle) {
    'fa-solid', 'solid', 'fas' => 'fas',
    'fa-regular', 'regular', 'far' => 'far',
    'fa-light', 'light', 'fal' => 'fal',
    'fa-brands', 'brands', 'fab' => 'fab',
    default => 'fas',
};

$sizeClass = match($size) {
    'xs' => 'w-3.5 h-3.5 text-[12px]',
    'sm' => 'w-4 h-4 text-[14px]',
    'lg' => 'w-6 h-6 text-[20px]',
    default => 'w-5 h-5 text-[16px]',
};
$baseClass = $sizeClass . ' ' . $styleClass . ' ' . $iconClass . ' inline-block leading-none';
@endphp
<i {{ $attributes->merge(['class' => $baseClass]) }} aria-hidden="true"></i>
