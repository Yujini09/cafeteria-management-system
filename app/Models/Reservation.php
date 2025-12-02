<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'event_name',
        'event_date',
        'event_time',  // This should be a string time like "07:00-10:00"
        'end_date',    // Add this if you want to store date range
        'day_times', // Add this
        'number_of_persons',
        'special_requests',
        'status',
        'decline_reason',
        // Additional fields from your form
        'contact_person',
        'department',
        'address',
        'email',
        'contact_number',
        'venue',
        'project_name',
        'account_code',
        // Legacy fields for backward compatibility
        'date',
        'time',
        'guests',
        // Add these to your Reservation model's $fillable array:
        'receipt_path',
        'receipt_uploaded_at',
        'payment_status', // 'pending', 'paid', 'overdue'
    ];

    protected $casts = [
        'event_date' => 'date',
        'receipt_uploaded_at' => 'datetime',
        'end_date' => 'date',
        'day_times' => 'array', // Add this cast
        // Don't cast event_time to datetime since it might contain JSON or time range string
        // 'event_time' => 'datetime', // Remove or comment this line
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }
    
    public function scopeStatus($q, $status)
    {
        if (in_array($status, ['pending','approved','declined'], true)) {
            $q->where('status', $status);
        }
        return $q;
    }
}