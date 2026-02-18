<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['menu_id','name','type'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Copy recipes from another menu item if this item has no recipes yet.
     */
    public function copyRecipesFrom(MenuItem $sourceItem)
    {
        if ($this->recipes()->exists()) {
            return; // Already has recipes, don't copy
        }

        foreach ($sourceItem->recipes as $sourceRecipe) {
            $this->recipes()->create([
                'inventory_item_id' => $sourceRecipe->inventory_item_id,
                'quantity_needed' => $sourceRecipe->quantity_needed,
                'unit' => $sourceRecipe->unit,
            ]);
        }
    }
}

