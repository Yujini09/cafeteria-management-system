<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['name','qty','unit','category','expiry_date'];

    protected $casts = [
        'qty' => 'decimal:3',
        'expiry_date' => 'date',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
