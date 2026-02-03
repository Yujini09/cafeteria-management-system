<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use App\Models\AuditTrail;
use App\Support\PasswordRules;
use App\Http\Controllers\Controller;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if ($user !== null && ! Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => PasswordRules::validationRules(true),
        ]);

        $data = $validator->validate();

        if ($user !== null) {
            $user->password = Hash::make($data['password']);
            $user->save();

            // Audit: record password update
            AuditTrail::create([
                'user_id' => $user->id,
                'action' => 'Updated password',
                'module' => 'users',
                'description' => 'updated password',
            ]);
        }

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }
}
