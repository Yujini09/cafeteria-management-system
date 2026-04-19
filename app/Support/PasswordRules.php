<?php

namespace App\Support;

/**
 * Single source of truth for password validation rules and real-time checks.
 * Used by Livewire password component and backend validation.
 */
final class PasswordRules
{
    public const MIN_LENGTH = 8;
    public const WEAK_PASSWORD_MESSAGE = 'Weak password. Use at least 8 characters and at least 1 number.';

    /**
     * Check password against each rule. Returns array of rule key => passed (bool).
     *
     * @return array<string, bool>
     */
    public static function check(string $password): array
    {
        return [
            'min' => strlen($password) >= self::MIN_LENGTH,
            'number' => (bool) preg_match('/[0-9]/', $password),
        ];
    }

    /**
     * Whether the password satisfies all required rules.
     */
    public static function isValid(string $password): bool
    {
        $results = self::check($password);
        return $results['min'] && $results['number'];
    }

    /**
     * Laravel validation rules for password (for use in FormRequest or controller).
     *
     * @return array<int, mixed>
     */
    public static function validationRules(bool $confirmed = false): array
    {
        $rules = [
            'required',
            'string',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! self::isValid((string) $value)) {
                    $fail(self::WEAK_PASSWORD_MESSAGE);
                }
            },
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }
}
