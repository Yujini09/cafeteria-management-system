{{-- Admin password input. When showRequirements is true, use Livewire for real-time rules; otherwise Blade-only with eye toggle. --}}
@props([
    'name' => 'password',
    'label' => 'Password',
    'showRequirements' => false,
    'helper' => null,
    'required' => true,
])

@if($showRequirements)
    {!! app('livewire')->mount('password-with-rules', [
        'name' => $name,
        'label' => $label,
        'showRequirements' => true,
        'required' => $required,
        'variant' => 'admin',
    ]) !!}
@else
    @php
        $hasError = $errors->has($name);
        $confirmedError = null;
        if (str_ends_with($name, '_confirmation')) {
            $baseName = \Illuminate\Support\Str::beforeLast($name, '_confirmation');
            $confirmedError = collect($errors->get($baseName))
                ->first(fn ($message) => \Illuminate\Support\Str::contains(strtolower($message), 'confirmation'));
        }
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
                autocomplete="{{ $name === 'password' ? 'new-password' : 'current-password' }}"
                {{ $attributes->merge(['class' => $inputClass]) }}
            >
            <button
                type="button"
                @click="show = !show"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-neutral-700 focus:outline-none transition-colors duration-200"
                :aria-label="show ? 'Hide password' : 'Show password'"
            >
                <x-ui.eye-toggle class="w-5 h-5" />
            </button>
        </div>
        @if($helper && !$hasError)
            <p class="text-xs text-admin-neutral-500">{{ $helper }}</p>
        @endif
        @error($name)
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
        @if(!$hasError && $confirmedError)
            <p class="text-sm text-red-600">{{ $confirmedError }}</p>
        @endif
    </div>
@endif
