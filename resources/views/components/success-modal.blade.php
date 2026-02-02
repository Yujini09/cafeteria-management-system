@props([
    'name',
    'title' => 'Success!',
    'maxWidth' => 'sm',
])

<x-admin.ui.modal :name="$name" :title="$title" variant="confirmation" :maxWidth="$maxWidth">
    @if($slot->isEmpty())
        <p class="text-sm text-admin-neutral-600">Action completed successfully.</p>
    @else
        {{ $slot }}
    @endif

    @if(isset($footer))
        <x-slot:footer>
            {{ $footer }}
        </x-slot:footer>
    @endif
</x-admin.ui.modal>
