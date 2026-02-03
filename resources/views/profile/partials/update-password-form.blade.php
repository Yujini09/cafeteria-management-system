<section 
    x-data="{ changed: false, showCurrent: false, showNew: false, showConfirm: false }" 
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
        <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
        <p class="mt-1 text-sm text-gray-600">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="relative">
            <x-input-label for="current_password" value="Current Password" />
            <x-text-input 
                id="current_password" name="current_password" 
                :type="showCurrent ? 'text' : 'password'"
                class="mt-1 block w-full pr-10"
                @input="changed = true"
                autocomplete="current-password"
            />
            <button type="button" @click="showCurrent = !showCurrent"
                class="absolute inset-y-0 right-2 flex items-center text-gray-600">
                👁️
            </button>
            <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
        </div>

        <!-- New Password -->
        <div class="relative">
            <x-input-label for="password" value="New Password" />
            <x-text-input 
                id="password" name="password" 
                :type="showNew ? 'text' : 'password'"
                class="mt-1 block w-full pr-10"
                @input="changed = true"
                autocomplete="new-password"
            />
            <button type="button" @click="showNew = !showNew"
                class="absolute inset-y-0 right-2 flex items-center text-gray-600">
                👁️
            </button>
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="relative">
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input 
                id="password_confirmation" name="password_confirmation" 
                :type="showConfirm ? 'text' : 'password'"
                class="mt-1 block w-full pr-10"
                @input="changed = true"
                autocomplete="new-password"
            />
            <button type="button" @click="showConfirm = !showConfirm"
                class="absolute inset-y-0 right-2 flex items-center text-gray-600">
                👁️
            </button>
            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" x-ref="saveBtn" disabled
                class="px-4 py-2 text-white rounded bg-gray-400 cursor-not-allowed">
                Save Password
            </button>
        </div>
    </form>
</section>
