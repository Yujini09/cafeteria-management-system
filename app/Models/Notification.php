<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'action',
        'module',
        'description',
        'metadata',
        'read'
    ];

    protected $casts = [
        'metadata' => 'array',
        'read' => 'boolean',
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for unread notifications
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    // Scope for specific module
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    // Scope for specific action
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
