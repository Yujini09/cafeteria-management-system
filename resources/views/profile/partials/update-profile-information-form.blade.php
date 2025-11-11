<section 
    x-data="{ changed: false }" 
    x-init="
        $watch('changed', value => {
            let btn = $refs.saveBtn;
            if (value) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                // MODIFIED: Use custom green hex codes for active state
                btn.classList.add('bg-gradient-to-r', 'from-[#00462E]', 'to-[#057C3C]', 'hover:from-[#057C3C]', 'hover:to-[#00462E]', 'shadow-lg');
            } else {
                btn.disabled = true;
                // MODIFIED: Remove custom green hex classes
                btn.classList.remove('bg-gradient-to-r', 'from-[#00462E]', 'to-[#057C3C]', 'hover:from-[#057C3C]', 'hover:to-[#00462E]', 'shadow-lg');
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Profile Information</h2>
        </div>
        <p class="text-sm text-gray-600 ml-11">
            Update your account's profile information and email address.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <div class="input-container">
                <input 
                    id="name" 
                    name="name" 
                    type="text"
                    class="modal-input pl-10"
                    value="{{ old('name', $user->name) }}"
                    required 
                    autofocus
                    @input="changed = true"
                />
                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="input-container">
                <input 
                    id="email" 
                    name="email" 
                    type="email"
                    class="modal-input pl-10"
                    value="{{ old('email', $user->email) }}"
                    required
                    @input="changed = true"
                />
                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button 
                type="submit" 
                x-ref="saveBtn" 
                disabled
                class="px-6 py-3 text-white rounded-xl font-semibold transition-all duration-300 bg-gray-300 cursor-not-allowed shadow-sm"
            >
                Save Changes
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

.input-container {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a3a3a3;
}

.modal-input {
    width: 100%;
    padding: 0.875rem 0.875rem 0.875rem 2.5rem;
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