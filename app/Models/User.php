<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

class User extends Authenticatable implements MustVerifyEmail
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    use HasFactory, Notifiable, MustVerifyEmailTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'contact_no',
        'department',
        'role',   // ✅ your manual role column
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    /**
     * Backward compatibility: allow ->contact_number to read the database column contact_no.
     */
    public function getContactNumberAttribute()
    {
        return $this->contact_no;
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
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail);
    }
}