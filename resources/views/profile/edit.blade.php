@extends('layouts.sidebar')

@section('page-title', 'Account Settings')

@section('content')
<style>
/* Modern Design Variables */
:root {
    --primary: #00462E;
    --primary-light: #057C3C;
    --accent: #FF6B35;
    --neutral-50: #fafafa;
    --neutral-100: #f5f5f5;
    --neutral-200: #e5e5e5;
    --neutral-300: #d4d4d4;
    --neutral-400: #a3a3a3;
    --neutral-500: #737373;
    --neutral-600: #525252;
    --neutral-700: #404040;
    --neutral-800: #262626;
    --neutral-900: #171717;
}

/* Modern Card Styles */
.modern-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #00462E 0%, #057C3C 100%);
}

/* Button Styles */
.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 70, 46, 0.2);
}

.btn-secondary {
    background: var(--neutral-100);
    color: var(--neutral-700);
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-secondary:hover {
    background: var(--neutral-200);
}

/* Modal Styles */
.modern-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    border: 1px solid var(--neutral-200);
}

.modal-input {
    width: 100%;
    padding: 0.875rem;
    border: 1px solid var(--neutral-300);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.modal-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

/* Header Styles */
.page-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon svg {
    color: white;
    width: 1.25rem;
    height: 1.25rem;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--neutral-900);
    letter-spacing: -0.5px;
}

/* Option Card Styles */
.option-card {
    background: var(--neutral-50);
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid var(--neutral-200);
    transition: all 0.3s ease;
    cursor: pointer;
    width: 100%;
    text-align: left;
}

.option-card:hover {
    background: var(--neutral-100);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    border-color: var(--primary-light);
}

.option-card-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.option-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.option-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.option-icon svg {
    color: white;
    width: 1.25rem;
    height: 1.25rem;
}

.option-text {
    font-size: 1rem;
    font-weight: 600;
    color: var(--neutral-800);
}

.option-arrow {
    color: var(--neutral-400);
    transition: transform 0.3s ease;
}

.option-card:hover .option-arrow {
    transform: translateX(4px);
    color: var(--primary);
}

/* Password Input Styles - Fixed */
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
    color: var(--neutral-400);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: color 0.2s ease;
    z-index: 10;
}

.password-toggle:hover {
    color: var(--primary);
}

.password-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--neutral-400);
    z-index: 5;
}

/* Fixed password input padding to prevent overlapping */
.password-input-container .modal-input {
    padding-left: 2.75rem !important;
    padding-right: 2.75rem !important;
}

/* Success Modal Styles */
.success-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    border: 1px solid var(--neutral-200);
    text-align: center;
    padding: 2rem;
}

.success-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.success-icon svg {
    color: white;
    width: 2rem;
    height: 2rem;
}

.success-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--neutral-900);
    margin-bottom: 0.5rem;
}

.success-message {
    color: var(--neutral-600);
    margin-bottom: 2rem;
}

/* Success button */
.btn-success {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}
</style>

<div class="modern-card p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0" style="max-width: calc(100vw - 12rem);" x-data="accountSettings()">
    <!-- Header -->
    <div class="page-header">
        <div class="header-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <h1 class="header-title">Account Settings</h1>
    </div>

    <!-- Option Lists -->
    <div class="space-y-4">
        <!-- Profile Settings Option -->
        <button type="button" @click="$dispatch('open-modal', 'profile-settings')" class="option-card">
            <div class="option-card-content">
                <div class="option-info">
                    <div class="option-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="option-text">Profile Settings</span>
                </div>
                <svg class="option-arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </button>

        <!-- Change Password Option -->
        <button type="button" @click="$dispatch('open-modal', 'change-password')" class="option-card">
            <div class="option-card-content">
                <div class="option-info">
                    <div class="option-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <span class="option-text">Change Password</span>
                </div>
                <svg class="option-arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </button>
    </div>

    <!-- Profile Settings Modal -->
    <x-modal name="profile-settings" class="w-full max-w-md mx-auto">
        <div class="modern-modal p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Profile Settings</h3>
                <button @click="$dispatch('close-modal', 'profile-settings')" class="text-gray-400 hover:text-gray-600 border-none p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6" id="profile-form">
                @csrf
                @method('patch')

                <!-- Full Name -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="modal-input @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Personal Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Personal Email</label>
                    @if($user->hasRole('admin'))
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                               class="modal-input bg-gray-100 text-gray-500 cursor-not-allowed" disabled readonly>
                        <p class="text-sm text-gray-600 mt-2">Contact superadmin to change email address</p>
                    @else
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                               class="modal-input @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" @click="$dispatch('close-modal', 'profile-settings')" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Change Password Modal -->
    <x-modal name="change-password" class="w-full max-w-md mx-auto">
        <div class="modern-modal p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Change Password</h3>
                <button @click="$dispatch('close-modal', 'change-password')" class="text-gray-400 hover:text-gray-600 border-none p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('password.update') }}" class="space-y-6" id="password-form">
                @csrf
                @method('put')

                <!-- Current Password -->
                <div class="space-y-2">
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <div class="password-input-container">
                        <input type="password" id="current_password" name="current_password"
                               class="modal-input @error('current_password') border-red-500 @enderror" required>
                        <svg class="password-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <button type="button" id="toggleCurrentPassword" class="password-toggle">
                            <svg id="eyeIconCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password"
                               class="modal-input @error('password') border-red-500 @enderror" required>
                        <svg class="password-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <button type="button" id="toggleNewPassword" class="password-toggle">
                            <svg id="eyeIconNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="modal-input @error('password_confirmation') border-red-500 @enderror" required>
                        <svg class="password-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <button type="button" id="toggleConfirmPassword" class="password-toggle">
                            <svg id="eyeIconConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" @click="$dispatch('close-modal', 'change-password')" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Success Modal -->
    <x-modal name="success-modal" class="w-full max-w-sm mx-auto">
        <div class="success-modal">
            <div class="success-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="success-title" x-text="successTitle">Success!</h3>
            <p class="success-message" x-text="successMessage">Your changes have been saved successfully.</p>
            <button type="button" @click="closeSuccessModal()" class="btn-success">
                Continue
            </button>
        </div>
    </x-modal>
</div>

<script>
// Toggle for current password
document.getElementById('toggleCurrentPassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('current_password');
    const eyeIcon = document.getElementById('eyeIconCurrent');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
});

// Toggle for new password
document.getElementById('toggleNewPassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIconNew');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
});

// Toggle for confirm password
document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const eyeIcon = document.getElementById('eyeIconConfirm');
    if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
    } else {
        confirmPasswordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
});

// Alpine.js component for account settings
function accountSettings() {
    return {
        successTitle: 'Success!',
        successMessage: 'Your changes have been saved successfully.',
        
        init() {
            // Profile form submission
            const profileForm = document.getElementById('profile-form');
            if (profileForm) {
                profileForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleProfileSubmit();
                });
            }

            // Password form submission
            const passwordForm = document.getElementById('password-form');
            if (passwordForm) {
                passwordForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handlePasswordSubmit();
                });
            }
        },

        handleProfileSubmit() {
            // Show loading state
            const submitBtn = document.querySelector('#profile-form .btn-primary');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Updating...';
            submitBtn.disabled = true;

            // Simulate API call (replace with actual form submission)
            setTimeout(() => {
                // Close profile modal
                this.$dispatch('close-modal', 'profile-settings');
                
                // Set success message
                this.successTitle = 'Profile Updated!';
                this.successMessage = 'Your profile information has been updated successfully.';
                
                // Show success modal
                this.$dispatch('open-modal', 'success-modal');
                
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Optional: Actually submit the form
                // profileForm.submit();
            }, 1500);
        },

        handlePasswordSubmit() {
            // Show loading state
            const submitBtn = document.querySelector('#password-form .btn-primary');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Updating...';
            submitBtn.disabled = true;

            // Simulate API call (replace with actual form submission)
            setTimeout(() => {
                // Close password modal
                this.$dispatch('close-modal', 'change-password');
                
                // Set success message
                this.successTitle = 'Password Updated!';
                this.successMessage = 'Your password has been changed successfully.';
                
                // Show success modal
                this.$dispatch('open-modal', 'success-modal');
                
                // Reset form and button state
                document.getElementById('password-form').reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Optional: Actually submit the form
                // passwordForm.submit();
            }, 1500);
        },

        closeSuccessModal() {
            this.$dispatch('close-modal', 'success-modal');
        }
    }
}
</script>
@endsection