<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory;

// app/Models/AuditTrail.php
protected $fillable = ['user_id','action','module','description'];

    public static function record(?int $userId, string $action, string $module, ?string $description = null): ?self
    {
        if (!$userId) {
            return null;
        }

        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
        ]);
    }

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
