<!-- reset-password.blade.php -->
<x-guest-layout>
    @php
        $passwordResetStatus = __('passwords.reset');
    @endphp
    <div class="min-h-screen flex items-center justify-center bg-green-950 relative overflow-hidden">
        <div class="absolute inset-0 opacity-30">
            <div class="w-96 h-96 bg-orange-700 rounded-full absolute -top-20 left-1/4 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            <div class="w-64 h-64 bg-orange-700 rounded-full absolute bottom-0 right-0 mix-blend-screen opacity-50 transform translate-x-1/4 translate-y-1/4"></div>
            <div class="w-80 h-80 bg-orange-700 rounded-full absolute top-1/4 left-0 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            <div class="w-40 h-40 bg-orange-700 rounded-full absolute -bottom-10 left-1/2 mix-blend-screen opacity-50 transform translate-x-1/4"></div>
        </div>

        <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-2xl z-10">
            <div class="w-full p-8 md:p-12 relative bg-green-100">
                <div class="absolute inset-0 opacity-50 overflow-hidden">
                    <div class="w-64 h-64 bg-green-200 rounded-full absolute -top-24 -right-24"></div>
                    <div class="w-48 h-48 bg-green-200 rounded-full absolute -bottom-16 -left-16"></div>
                </div>

                <div class="relative z-10">
                    <div class="text-left mb-10">
                        <h2 class="text-green-900 text-4xl font-extrabold mb-2">Reset Password</h2>
                        <p class="text-green-700 text-lg">Create your new password</p>
                    </div>

                    <x-success-modal name="password-reset-success" title="Success!" maxWidth="sm">
                        <p class="text-sm text-green-700">Password reset successfully. Redirecting to login...</p>
                    </x-success-modal>

                    @if(session('status') !== $passwordResetStatus)
                        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="mb-6">
                                <x-input-label for="email" :value="__('Email')" class="text-green-700 font-medium mb-2" />
                                <div class="relative">
                                    <x-text-input id="email"
                                        class="block mt-1 w-full pl-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900 bg-green-50 text-green-700 cursor-not-allowed"
                                        type="email"
                                        name="email"
                                        :value="old('email', $request->email)"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        readonly />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
                            </div>

                            <div class="mb-6">
                                {!! app('livewire')->mount('password-with-rules', [
                                    'name' => 'password',
                                    'label' => __('Password'),
                                    'showRequirements' => true,
                                    'required' => true,
                                    'variant' => 'auth',
                                ]) !!}
                            </div>

                            <div class="mb-6">
                                {!! app('livewire')->mount('password-with-rules', [
                                    'name' => 'password_confirmation',
                                    'label' => __('Confirm Password'),
                                    'showRequirements' => false,
                                    'required' => true,
                                    'variant' => 'auth',
                                ]) !!}
                                @php
                                    $confirmPasswordErrors = $errors->get('password_confirmation');
                                    $confirmedError = collect($errors->get('password'))
                                        ->first(fn ($message) => \Illuminate\Support\Str::contains(strtolower($message), 'confirmation'));
                                @endphp
                                @if (!empty($confirmPasswordErrors))
                                    <x-input-error :messages="$confirmPasswordErrors" class="mt-2" />
                                @elseif (!empty($confirmedError))
                                    <x-input-error :messages="[$confirmedError]" class="mt-2" />
                                @endif
                            </div>

                            <div>
                                <x-primary-button class="w-full justify-center bg-orange-500 hover:bg-orange-600 focus:ring-orange-500 h-12 text-lg font-semibold rounded-lg shadow-md transition duration-200 text-white">
                                    {{ __('Reset Password') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('status') === $passwordResetStatus)
    <script>
        window.addEventListener('load', function () {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'password-reset-success' }));
            }, 50);
        });
    </script>
    @endif
</x-guest-layout>
