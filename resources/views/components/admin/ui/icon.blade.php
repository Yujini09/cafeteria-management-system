{{--
  Admin icon: single style and size. Uses Font Awesome to match sidebar.
  Usage: <x-admin.ui.icon name="user" /> or <x-admin.ui.icon name="fa-plus" class="text-admin-primary" />
  name: Font Awesome icon name without fa- prefix (e.g. user, plus, trash) or full class (fa-plus).
--}}
@props([
    'name' => '',
    'style' => 'far', // fas | far | fal
    'size' => 'default', // default (w-5 h-5) | sm (w-4 h-4) | lg (w-6 h-6)
])

@php
$iconClass = str_starts_with($name, 'fa-') ? $name : 'fa-' . $name;
$sizeClass = match($size) {
    'sm' => 'w-4 h-4 text-[14px]',
    'lg' => 'w-6 h-6 text-[20px]',
    default => 'w-5 h-5 text-[16px]',
};
$baseClass = $sizeClass . ' ' . $style . ' ' . $iconClass . ' inline-block leading-none';
@endphp
<i {{ $attributes->merge(['class' => $baseClass]) }} aria-hidden="true"></i>
