<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements MustVerifyEmail
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    use HasFactory, Notifiable, MustVerifyEmailTrait;

    private const PENDING_ROLES = ['admin_pending', 'customer_pending'];
    private const OAUTH_ONLY_PASSWORD_PLACEHOLDER = '__oauth_only_account_without_local_password__';

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'contact_no',
        'phone',      // Add this so the fill() method accepts the form input name
        'department',
        'role',   // ✅ your manual role column
        'google_id',
        'must_change_password',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'must_change_password' => 'boolean',
    ];
    /**
     * MUTATOR: This magic function runs automatically.
     * When the controller tries to save 'phone', this puts the data into 'contact_no'.
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['contact_no'] = $value;
    }

    /**
     * ACCESSOR: When you call $user->phone, it reads from contact_no.
     */
    public function getPhoneAttribute()
    {
        return $this->attributes['contact_no'] ?? null;
    }

    /**
     * Simple replacement for Spatie hasRole() when the package is not installed.
     * Checks the `role` string column on the users table.
     */
    public function hasRole(string $role): bool
    {
        return isset($this->role) && $this->role === $role;
    }

    /**
     * Simple replacement for Spatie assignRole() when the package is not installed.
     * This will set the `role` column and save the model.
     */
    public function assignRole(string $role)
    {
        $this->role = $role;
        return $this->save();
    }

    public function isPendingAccount(): bool
    {
        return in_array($this->role, self::PENDING_ROLES, true);
    }

    public function hasLocalPassword(): bool
    {
        if (! filled($this->password)) {
            return false;
        }

        return ! $this->usesOAuthOnlyPassword();
    }

    public static function makeOauthOnlyPassword(): string
    {
        return Hash::make(self::OAUTH_ONLY_PASSWORD_PLACEHOLDER);
    }

    public function usesOAuthOnlyPassword(): bool
    {
        return filled($this->google_id)
            && filled($this->password)
            && Hash::check(self::OAUTH_ONLY_PASSWORD_PLACEHOLDER, (string) $this->password);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin_pending' => 'Admin',
            'customer_pending' => 'Customer',
            default => ucfirst((string) $this->role),
        };
    }

    public function getRoleFilterValueAttribute(): string
    {
        return match ($this->role) {
            'admin_pending' => 'admin',
            'customer_pending' => 'customer',
            default => (string) $this->role,
        };
    }

    public function getAccountStatusLabelAttribute(): string
    {
        return $this->isPendingAccount() ? 'Pending' : 'Active';
    }

    /**
     * Backward compatibility: allow ->contact_number to read the database column contact_no.
     */
    public function getContactNumberAttribute()
    {
        return $this->attributes['contact_no'] ?? null;
    }

    /**
     * Backward compatibility: set contact_no when code assigns contact_number.
     */
    public function setContactNumberAttribute($value)
    {
        $this->attributes['contact_no'] = $value;
    }

    /**
     * Get the reservations for the user.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(bool $force = false): void
    {
        $cooldownSeconds = max(0, (int) config('auth.verification_notification_cooldown', 30));
        $cacheKey = 'email_verification_last_sent:user:'.$this->getKey();

        if (!$force && $cooldownSeconds > 0 && Cache::has($cacheKey)) {
            return;
        }

        $this->notify(new VerifyEmail);

        if ($cooldownSeconds > 0) {
            Cache::put($cacheKey, now()->timestamp, now()->addSeconds($cooldownSeconds));
        }
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}

