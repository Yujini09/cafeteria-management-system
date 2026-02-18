<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        // Only require email validation if the user is not an admin
        if (!$user || !$user->hasRole('admin')) {
            $rules['email'] = [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(\Illuminate\Support\Facades\Auth::id() ?? null),
            ];
        }

        return $rules;
    }
}
