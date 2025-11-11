<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name','description','price','meal_time','type'];

    public function items(){ return $this->hasMany(MenuItem::class); }

    public function scopeMeal($q, $meal) {
        $allowed = ['breakfast','am_snacks','lunch','pm_snacks','dinner'];
        if (in_array($meal, $allowed, true)) $q->where('meal_time', $meal);
        return $q;
    }
}
