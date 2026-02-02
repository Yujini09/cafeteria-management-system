<?php

namespace App\Support;

/**
 * Single source of truth for password validation rules and real-time checks.
 * Used by Livewire password component and backend validation.
 */
final class PasswordRules
{
    public const MIN_LENGTH = 8;

    /**
     * Rule keys and labels for UI (check/cross).
     *
     * @return array<string, string>
     */
    public static function ruleLabels(): array
    {
        return [
            'min' => 'At least ' . self::MIN_LENGTH . ' characters',
            'number' => 'At least one number',
            'special' => 'At least one special character',
            'uppercase' => 'At least one uppercase letter (optional)',
        ];
    }

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
            'special' => (bool) preg_match('/[^A-Za-z0-9]/', $password),
            'uppercase' => (bool) preg_match('/[A-Z]/', $password),
        ];
    }

    /**
     * Whether the password satisfies all required rules (uppercase is optional).
     */
    public static function isValid(string $password): bool
    {
        $results = self::check($password);
        return $results['min'] && $results['number'] && $results['special'];
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
            'min:' . self::MIN_LENGTH,
            'regex:/[0-9]/',
            'regex:/[^A-Za-z0-9]/',
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }
}
