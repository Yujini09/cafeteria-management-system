<?php
namespace App\Models;

use App\Support\RecipeUnit;
use Illuminate\Validation\ValidationException;
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

        $sourceItem->loadMissing('recipes.inventoryItem');

        $payloads = [];

        foreach ($sourceItem->recipes as $sourceRecipe) {
            $inventoryItem = $sourceRecipe->inventoryItem;
            if (!$inventoryItem) {
                throw ValidationException::withMessages([
                    'items' => [
                        "Cannot auto-copy ingredient for \"{$this->name}\" because the linked inventory item was not found.",
                    ],
                ]);
            }

            $normalizedRecipeUnit = RecipeUnit::normalize($sourceRecipe->unit) ?? RecipeUnit::normalize($inventoryItem->unit);
            $stockUnit = RecipeUnit::display($inventoryItem->unit);

            if (!RecipeUnit::isAllowedRecipeUnit($normalizedRecipeUnit) || !RecipeUnit::areCompatible($normalizedRecipeUnit, $stockUnit)) {
                throw ValidationException::withMessages([
                    'items' => [
                        "Cannot auto-copy ingredient \"{$inventoryItem->name}\" for \"{$this->name}\". Recipe unit and stock unit are incompatible.",
                    ],
                ]);
            }

            $payloads[] = [
                'inventory_item_id' => $sourceRecipe->inventory_item_id,
                'quantity_needed' => $sourceRecipe->quantity_needed,
                'unit' => $normalizedRecipeUnit,
            ];
        }

        foreach ($payloads as $payload) {
            $this->recipes()->create($payload);
        }
    }
}
