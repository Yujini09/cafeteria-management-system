<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryUsageLog extends Model
{
    public const TYPE_AUTO_DEDUCT = 'auto_deduct';
    public const TYPE_MANUAL_ADJUSTMENT = 'manual_adjustment';

    protected $fillable = [
        'inventory_item_id',
        'item_name',
        'type',
        'quantity_change',
        'new_balance',
        'reservation_id',
        'user_id',
    ];

    protected $casts = [
        'quantity_change' => 'float',
        'new_balance' => 'float',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
