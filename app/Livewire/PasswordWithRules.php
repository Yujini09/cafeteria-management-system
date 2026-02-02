<?php

namespace App\Livewire;

use App\Support\PasswordRules;
use Livewire\Component;

class PasswordWithRules extends Component
{
    public string $password = '';

    public string $name = 'password';

    public string $label = 'Password';

    public bool $showRequirements = true;

    public bool $required = true;

    /** 'admin' | 'auth' - for styling (admin tokens vs auth green/orange) */
    public string $variant = 'admin';

    public function updatedPassword(): void
    {
        // Invalidate cached computed property so rules re-evaluate as you type.
        unset($this->rulesResult);
    }

    /**
     * Rule results for current password (for Blade).
     *
     * @return array<string, bool>
     */
    public function getRulesResultProperty(): array
    {
        return PasswordRules::check($this->password);
    }

    public function isValid(): bool
    {
        return PasswordRules::isValid($this->password);
    }

    public function render()
    {
        return view('livewire.password-with-rules');
    }
}
