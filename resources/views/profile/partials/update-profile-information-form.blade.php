<section 
    x-data="{ changed: false }" 
    x-init="
        $watch('changed', value => {
            let btn = $refs.saveBtn;
            if (value) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                btn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                btn.disabled = true;
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
        });
    "
>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
        <p class="mt-1 text-sm text-gray-600">
            Update your account's profile information and email address.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input 
                id="name" name="name" type="text"
                class="mt-1 block w-full"
                value="{{ old('name', $user->name) }}"
                required autofocus
                @input="changed = true"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input 
                id="email" name="email" type="email"
                class="mt-1 block w-full"
                value="{{ old('email', $user->email) }}"
                required
                @input="changed = true"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" x-ref="saveBtn" disabled
                class="px-4 py-2 text-white rounded bg-gray-400 cursor-not-allowed">
                Save Changes
            </button>
        </div>
    </form>
</section>
