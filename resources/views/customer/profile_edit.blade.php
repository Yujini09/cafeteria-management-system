@extends('layouts.app')

@section('title', 'My Profile - CLSU RET Cafeteria')

@section('styles')
    .bg-clsu-green { background-color: #057C3C; }
    .text-clsu-green { color: #057C3C; }
    .border-clsu-green { border-color: #057C3C; }
    .hover\:bg-clsu-green-dark:hover { background-color: #046c33; }
    
    input:focus, textarea:focus {
        --tw-ring-color: #057C3C !important;
        --tw-ring-opacity: 0.2;
        outline: none;
        box-shadow: 0 0 0 3px rgba(5, 124, 60, 0.2);
        border-color: #057C3C;
    }
    
    .nav-tab-btn { transition: all 0.2s ease; }
    .nav-tab-btn.active { background-color: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
    .nav-tab-btn:not(.active):hover { background-color: #f9fafb; color: #374151; }
    
    [x-cloak] { display: none !important; }
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 font-poppins" 
     x-data="{ 
         activeTab: '{{ session('status') === 'password-updated' || $errors->hasBag('updatePassword') ? 'security' : 'profile' }}',
         isEditing: {{ $errors->any() && !$errors->hasBag('updatePassword') ? 'true' : 'false' }}
     }">
    
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-8">
                <div class="mb-6">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors text-sm font-medium">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Back to Home</span>
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="bg-green-100 p-3 rounded-xl text-clsu-green">
                        <i class="fa-regular fa-id-card text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
                        <p class="text-gray-500 text-sm mt-1">Manage your account information and security settings</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                        <div class="flex flex-col items-center text-center mb-8">
                            <div class="relative w-32 h-32 mb-4 group">
                                <div class="w-full h-full rounded-full overflow-hidden border-4 border-white shadow-lg bg-green-50 flex items-center justify-center">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Profile" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-5xl font-bold text-clsu-green">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <form id="avatar-form" action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                    <template x-if="isEditing">
                                        <label for="avatar-upload" class="absolute bottom-1 right-1 bg-gray-900 text-white p-2.5 rounded-full shadow-md cursor-pointer hover:bg-clsu-green transition-colors duration-200 border-2 border-white" title="Change Photo">
                                            <i class="fa-solid fa-camera text-xs"></i>
                                        </label>
                                    </template>
                                    <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
                                </form>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                            <p class="text-sm text-gray-500 break-all">{{ Auth::user()->email }}</p>
                            <div class="mt-4"><span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-full"><i class="fa-solid fa-shield-halved"></i> Customer</span></div>
                        </div>
                        <hr class="border-gray-100 my-6">
                        <nav class="space-y-2">
                            <button @click="activeTab = 'profile'; isEditing = false;" :class="activeTab === 'profile' ? 'active' : ''" class="nav-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-gray-600">
                                <i class="fa-regular fa-user w-5 text-center"></i> Personal Info
                            </button>
                            <button @click="activeTab = 'security'; isEditing = false;" :class="activeTab === 'security' ? 'active' : ''" class="nav-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-gray-600">
                                <i class="fa-solid fa-lock w-5 text-center"></i> Security
                            </button>
                        </nav>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="flex justify-between items-center text-xs text-gray-400">
                                <span>Member Since</span>
                                <span class="font-semibold text-gray-600">{{ Auth::user()->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    @if (session('status') === 'profile-updated')
                        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3" x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                            <div><h4 class="font-bold text-sm">Success!</h4><p class="text-xs">Your profile information has been updated.</p></div>
                        </div>
                    @endif
                    @if (session('status') === 'password-updated')
                        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3" x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                            <div><h4 class="font-bold text-sm">Success!</h4><p class="text-xs">Your password has been changed securely.</p></div>
                        </div>
                    @endif
                    @if($errors->any() && session('status') !== 'password-updated')
                         <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                            <div><h4 class="font-bold text-sm">Please check the form for errors</h4><ul class="list-disc list-inside text-xs mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                        </div>
                    @endif

                    <div x-show="activeTab === 'profile'" x-transition.opacity.duration.300ms>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            
                            {{-- HEADER --}}
                            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div><h3 class="text-xl font-bold text-gray-900 flex items-center gap-2"><i class="fa-regular fa-address-card text-clsu-green"></i> Personal Information</h3><p class="text-gray-500 text-sm mt-1">View and manage your personal details.</p></div>
                                <div>
                                    <template x-if="!isEditing">
                                        <button @click="isEditing = true" class="px-5 py-2.5 bg-clsu-green text-white rounded-xl hover:bg-green-700 transition-colors flex items-center gap-2 text-sm font-semibold shadow-sm"><i class="fa-regular fa-pen-to-square"></i> Edit Profile</button>
                                    </template>
                                    <template x-if="isEditing">
                                        <button @click="isEditing = false; window.location.reload();" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors flex items-center gap-2 text-sm font-medium"><i class="fa-solid fa-xmark"></i> Cancel</button>
                                    </template>
                                </div>
                            </div>

                            {{-- VIEW MODE --}}
                            <div class="p-6 md:p-8 space-y-6" x-show="!isEditing">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                    <div><label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Full Name</label><p class="text-gray-900 font-medium text-lg border-b border-gray-100 pb-2">{{ $user->name }}</p></div>
                                    <div><label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email Address</label><p class="text-gray-900 font-medium text-lg border-b border-gray-100 pb-2">{{ $user->email }}</p></div>
                                    <div><label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Phone Number</label><p class="text-gray-900 font-medium text-lg border-b border-gray-100 pb-2">{{ $user->phone ?? 'Not Set' }}</p></div>
                                    <div><label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date of Birth</label><p class="text-gray-900 font-medium text-lg border-b border-gray-100 pb-2">{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('F d, Y') : 'Not Set' }}</p></div>
                                    <div class="md:col-span-2"><label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Complete Address</label><p class="text-gray-900 font-medium text-base border-b border-gray-100 pb-2">{{ $user->address ?? 'Not Set' }}</p></div>
                                </div>
                            </div>

                            {{-- EDIT MODE --}}
                            <form method="post" action="{{ route('profile.update') }}" class="p-6 md:p-8 space-y-6" id="profile-update-form" x-show="isEditing">
                                @csrf
                                @method('patch')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="col-span-1">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                        <div class="relative"><i class="fa-regular fa-user absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"></i><input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-clsu-green transition-all bg-white"></div>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address <span class="text-xs text-red-500 font-normal ml-1">(Locked)</span></label>
                                        <div class="relative"><i class="fa-regular fa-envelope absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"></i><input type="email" name="email" value="{{ $user->email }}" readonly class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed"><i class="fa-solid fa-lock absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i></div>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                        <div class="relative"><i class="fa-solid fa-phone absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"></i><input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-clsu-green transition-all" placeholder="09xxxxxxxxx" maxlength="11" oninput="validatePhone(this)"></div>
                                        <p id="phone-error" class="text-red-500 text-xs mt-1 hidden">Please enter a valid 11-digit number starting with 09.</p>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth</label>
                                        <div class="relative"><i class="fa-regular fa-calendar absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"></i><input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date) }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}" onchange="validateBirthday(this)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-clsu-green transition-all text-gray-700"></div>
                                        <p id="dob-error" class="text-red-500 text-xs mt-1 hidden">You must be at least 18 years old.</p>
                                    </div>
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Complete Address</label>
                                        <textarea name="address" rows="2" class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-clsu-green transition-all resize-none">{{ old('address', $user->address) }}</textarea>
                                    </div>
                                </div>
                                <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                                    <button type="button" @click="isEditing = false" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">Cancel</button>
                                    <button type="submit" id="save-btn" class="px-8 py-3 bg-clsu-green text-white font-bold rounded-xl hover:bg-clsu-green-dark shadow-lg shadow-green-500/20 transition-all transform hover:-translate-y-0.5">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div x-show="activeTab === 'security'" x-cloak x-transition.opacity.duration.300ms>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-gray-900">Change Password</h3>
                                <p class="text-gray-500 text-sm mt-1">Ensure your account is secure with a strong password.</p>
                            </div>

                            <form method="post" action="{{ route('password.update') }}" class="p-6 md:p-8 space-y-6">
                                @csrf
                                @method('put')

                                <div class="space-y-6 max-w-lg">
                                    <div x-data="{ show: false }">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="current_password" autocomplete="current-password" required 
                                                class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-xl text-gray-800 focus:ring-2 focus:ring-green-500/20 focus:border-clsu-green transition-all">
                                            
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                            </button>
                                        </div>
                                        @if($errors->updatePassword->has('current_password'))
                                            <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                                        @endif
                                    </div>

                                    <div x-data="{ show: false }">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="password" id="new_password" autocomplete="new-password" required 
                                                class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-xl text-gray-800 focus:ring-2 focus:ring-green-500/20 focus:border-clsu-green transition-all"
                                                onkeyup="checkPasswordStrength(this.value)">
                                            
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                            </button>
                                        </div>
                                        @if($errors->updatePassword->has('password'))
                                            <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
                                        @endif

                                        {{-- Visual Strength Bar --}}
                                        <div class="flex gap-1.5 h-1.5 mt-3 px-1">
                                            <div id="bar-1" class="flex-1 rounded-full bg-gray-100 transition-colors duration-300"></div>
                                            <div id="bar-2" class="flex-1 rounded-full bg-gray-100 transition-colors duration-300"></div>
                                            <div id="bar-3" class="flex-1 rounded-full bg-gray-100 transition-colors duration-300"></div>
                                            <div id="bar-4" class="flex-1 rounded-full bg-gray-100 transition-colors duration-300"></div>
                                        </div>

                                        {{-- Requirement Tags --}}
                                        <div class="flex flex-wrap gap-2 pt-1">
                                            <span id="req-len" class="text-[9px] font-bold uppercase px-2 py-1 rounded-md border border-gray-200 text-gray-400 bg-gray-50 transition-all">8+ Char</span>
                                            <span id="req-up" class="text-[9px] font-bold uppercase px-2 py-1 rounded-md border border-gray-200 text-gray-400 bg-gray-50 transition-all">Uppercase</span>
                                            <span id="req-num" class="text-[9px] font-bold uppercase px-2 py-1 rounded-md border border-gray-200 text-gray-400 bg-gray-50 transition-all">Number</span>
                                            <span id="req-spec" class="text-[9px] font-bold uppercase px-2 py-1 rounded-md border border-gray-200 text-gray-400 bg-gray-50 transition-all">Symbol</span>
                                        </div>
                                    </div>

                                    <div x-data="{ show: false }">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password" required 
                                                class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-xl text-gray-800 focus:ring-2 focus:ring-green-500/20 focus:border-clsu-green transition-all">
                                            
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                            </button>
                                        </div>
                                        @if($errors->updatePassword->has('password_confirmation'))
                                            <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-gray-100 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-700 shadow-lg shadow-gray-500/20 transition-all">Update Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function validatePhone(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
        const errorMsg = document.getElementById('phone-error');
        const saveBtn = document.getElementById('save-btn');
        if (input.value.length > 0 && (!input.value.startsWith('09') || input.value.length !== 11)) {
            errorMsg.classList.remove('hidden');
            input.classList.add('border-red-500');
            input.classList.remove('border-gray-300');
            saveBtn.disabled = true;
            saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorMsg.classList.add('hidden');
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function validateBirthday(input) {
        const selectedDate = new Date(input.value);
        const today = new Date();
        const minAgeDate = new Date();
        minAgeDate.setFullYear(today.getFullYear() - 18);

        const errorMsg = document.getElementById('dob-error');
        const saveBtn = document.getElementById('save-btn');

        if (selectedDate > minAgeDate) {
            errorMsg.classList.remove('hidden');
            input.classList.add('border-red-500');
            input.classList.remove('border-gray-300');
            saveBtn.disabled = true;
            saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorMsg.classList.add('hidden');
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function checkPasswordStrength(password) {
        const requirements = {
            len: password.length >= 8,
            up: /[A-Z]/.test(password),
            num: /[0-9]/.test(password),
            spec: /[^A-Za-z0-9]/.test(password)
        };

        updateTagUI('req-len', requirements.len);
        updateTagUI('req-up', requirements.up);
        updateTagUI('req-num', requirements.num);
        updateTagUI('req-spec', requirements.spec);

        let score = Object.values(requirements).filter(Boolean).length;
        const colors = ['#f3f4f6', '#ef4444', '#f59e0b', '#10b981', '#059669'];
        
        for(let i = 1; i <= 4; i++) {
            const bar = document.getElementById('bar-' + i);
            bar.style.backgroundColor = (i <= score) ? colors[score] : '#f3f4f6';
        }
    }

    function updateTagUI(id, isValid) {
        const el = document.getElementById(id);
        if (isValid) {
            el.classList.remove('text-gray-400', 'border-gray-200', 'bg-gray-50');
            el.classList.add('text-green-700', 'border-green-200', 'bg-green-50');
        } else {
            el.classList.add('text-gray-400', 'border-gray-200', 'bg-gray-50');
            el.classList.remove('text-green-700', 'border-green-200', 'bg-green-50');
        }
    }
</script>
@endsection