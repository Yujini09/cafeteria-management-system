<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    // This allows the controller to save these specific fields
    protected $fillable = [
        'name', 
        'email', 
        'message', 
        'is_read'
    ];
}