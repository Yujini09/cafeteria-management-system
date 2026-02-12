<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'reference_number',
        'department_office',
        'payer_name',
        'account_code',
        'amount',
        'status',
        'reviewed_by',
        'reviewed_at',
        'notes',
        'receipt_path',
        'receipt_uploaded_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'receipt_uploaded_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
