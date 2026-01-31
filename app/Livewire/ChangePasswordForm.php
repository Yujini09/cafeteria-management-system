<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\AuditTrail;
use Livewire\Component;

class ChangePasswordForm extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value !== '' && ! Hash::check($value, Auth::user()?->password ?? '')) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'password' => ['required', Password::defaults()],
            'password_confirmation' => ['required'],
        ]);

        if ($this->password !== $this->password_confirmation) {
            $this->addError('password_confirmation', 'The password field confirmation does not match.');
            return;
        }

        $user = Auth::user();
        if ($user) {
            $user->password = Hash::make($this->password);
            $user->save();

            AuditTrail::create([
                'user_id' => $user->id,
                'action' => 'Updated password',
                'module' => 'users',
                'description' => 'updated password',
            ]);
        }

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('close-admin-modal', 'change-password');
        $this->dispatch('admin-toast', type: 'success', message: 'Password successfully changed.');
    }

    public function render()
    {
        return view('livewire.change-password-form');
    }
}
