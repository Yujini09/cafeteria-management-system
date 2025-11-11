<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;   // ✅ Import the correct View

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

        $user?->fill($request->validated());

        if ($user?->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user?->save();

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

    /** @var \App\Models\User|null $user */
    $user = \Illuminate\Support\Facades\Auth::user();

        Auth::logout();

        $user->delete();

        Session::invalidate();
        Session::regenerateToken();

        return Redirect::to('/');
    }
}
