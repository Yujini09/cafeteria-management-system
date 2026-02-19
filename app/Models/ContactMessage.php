<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'email', 
        'message', 
        'status' // Changed from is_read to status (UNREAD, READ, REPLIED)
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}