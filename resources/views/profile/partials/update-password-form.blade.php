[file name]: update-password-form.blade.php
[file content begin]
<section 
    x-data="{ changed: false, showCurrent: false, showNew: false, showConfirm: false }" 
    x-init="
        $watch('changed', value => {
            let btn = $refs.saveBtn;
            if (value) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                btn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800', 'shadow-lg');
            } else {
                btn.disabled = true;
                btn.classList.remove('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800', 'shadow-lg');
                btn.classList.add('bg-gray-300', 'cursor-not-allowed');
            }
        });
    "
    class="modern-card p-6"
>
    <header class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="header-icon-small">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Update Password</h2>
        </div>
        <p class="text-sm text-gray-600 ml-11">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="space-y-2">
            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
            <div class="password-input-container">
                <input 
                    id="current_password" 
                    name="current_password" 
                    :type="showCurrent ? 'text' : 'password'"
                    class="modal-input pl-10 pr-10"
                    @input="changed = true"
                    autocomplete="current-password"
                    required
                />
                <svg class="password-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <button type="button" @click="showCurrent = !showCurrent" class="password-toggle">
                    <svg :class="showCurrent ? 'text-green-600' : 'text-gray-400'" class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!showCurrent" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        <path x-show="showCurrent" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    </svg>
                </button>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('current_password')" />
        </div>

        <!-- New Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
            <div class="password-input-container">
                <input 
                    id="password" 
                    name="password" 
                    :type="showNew ? 'text' : 'password'"
                    class="modal-input pl-10 pr-10"
                    @input="changed = true"
                    autocomplete="new-password"
                    required
                />
                <svg class="password-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <button type="button" @click="showNew = !showNew" class="password-toggle">
                    <svg :class="showNew ? 'text-green-600' : 'text-gray-400'" class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!showNew" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        <path x-show="showNew" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    </svg>
                </button>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <div class="password-input-container">
                <input 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    :type="showConfirm ? 'text' : 'password'"
                    class="modal-input pl-10 pr-10"
                    @input="changed = true"
                    autocomplete="new-password"
                    required
                />
                <svg class="password-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <button type="button" @click="showConfirm = !showConfirm" class="password-toggle">
                    <svg :class="showConfirm ? 'text-green-600' : 'text-gray-400'" class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!showConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        <path x-show="showConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    </svg>
                </button>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button 
                type="submit" 
                x-ref="saveBtn" 
                disabled
                class="px-6 py-3 text-white rounded-xl font-semibold transition-all duration-300 bg-gray-300 cursor-not-allowed shadow-sm"
            >
                Save Password
            </button>
        </div>
    </form>
</section>

<style>
.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid #f5f5f5;
}

.header-icon-small {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #00462E 0%, #057C3C 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon-small svg {
    color: white;
    width: 1.25rem;
    height: 1.25rem;
}

.password-input-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: color 0.2s ease;
}

.password-toggle:hover {
    color: #00462E;
}

.password-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a3a3a3;
}

.modal-input {
    width: 100%;
    padding: 0.875rem 2.5rem 0.875rem 2.5rem;
    border: 1px solid #d4d4d4;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.modal-input:focus {
    outline: none;
    border-color: #00462E;
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}
</style>
[file content end]