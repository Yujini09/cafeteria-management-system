<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Notifications\PasswordChangedNotification;
use App\Support\AuditDictionary;
use App\Support\PasswordRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            $wasForceChangeRequired = (bool) $user->must_change_password;
            $user->password = Hash::make($data['password']);
            $user->must_change_password = false;
            $user->save();

            $user->notify(new PasswordChangedNotification(
                changeSource: $wasForceChangeRequired ? 'force-change' : 'profile-update'
            ));

            AuditTrail::record(
                $user->id,
                AuditDictionary::UPDATED_PASSWORD,
                AuditDictionary::MODULE_PROFILE,
                'updated account password'
            );
        }

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }
}
