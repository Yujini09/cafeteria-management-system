<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AuditTrail;
use App\Support\AuditDictionary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password; // Ensure this is imported
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // If admin, show admin edit page
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            return view('admin.edit', ['user' => $user]);
        }

        // If customer, show customer profile edit
        return view('customer.profile_edit', ['user' => $user]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::UPDATED_PROFILE,
            AuditDictionary::MODULE_PROFILE,
            'updated profile information'
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        // We explicitly define rules here to accept 'Ts12131989'
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed', 
                // Define specific rules instead of defaults
                Password::min(8)
                    ->letters()
                    ->mixedCase() // Requires Uppercase & Lowercase (Ts...)
                    ->numbers()   // Requires Numbers (12131989)
                    // ->symbols() // REMOVED: This allows passwords without special chars
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::UPDATED_PASSWORD,
            AuditDictionary::MODULE_PROFILE,
            'updated account password'
        );

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Real-time validation for current password (admin/superadmin settings modal).
     */
    public function checkCurrentPassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        $user = $request->user();
        $isValid = $user !== null && Hash::check((string) $request->input('current_password'), (string) $user->password);

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? null : 'The current password is incorrect.',
        ]);
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // Max 2MB
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();

            AuditTrail::record(
                Auth::id(),
                AuditDictionary::UPDATED_AVATAR,
                AuditDictionary::MODULE_PROFILE,
                'updated profile avatar'
            );
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
