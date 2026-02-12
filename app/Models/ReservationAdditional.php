<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationAdditional extends Model
{
    protected $fillable = [
        'reservation_id',
        'name',
        'price',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
