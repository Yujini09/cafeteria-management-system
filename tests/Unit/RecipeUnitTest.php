<?php

use App\Support\RecipeUnit;

it('recognizes recipe units that are compatible with stock units', function () {
    expect(RecipeUnit::isAllowedRecipeUnit('ml'))->toBeTrue();
    expect(RecipeUnit::isAllowedRecipeUnit('pieces'))->toBeTrue();
    expect(RecipeUnit::isAllowedRecipeUnit('ounces'))->toBeFalse();

    expect(RecipeUnit::areCompatible('ml', 'Liters'))->toBeTrue();
    expect(RecipeUnit::areCompatible('g', 'Kgs'))->toBeTrue();
    expect(RecipeUnit::areCompatible('pc', 'Pieces'))->toBeTrue();
    expect(RecipeUnit::areCompatible('packs', 'Packs'))->toBeTrue();
    expect(RecipeUnit::areCompatible('ml', 'Kgs'))->toBeFalse();
    expect(RecipeUnit::areCompatible('packs', 'Pieces'))->toBeFalse();
});

it('converts recipe quantities into stock units for deduction', function () {
    $totalNeededRecipe = 15.0 * 10;
    $deductionInStockUnit = RecipeUnit::convertToStockUnit($totalNeededRecipe, 'ml', 'Liters');

    expect($totalNeededRecipe)->toBe(150.0);
    expect($deductionInStockUnit)->toEqualWithDelta(0.15, 0.000001);
    expect(1.0 - $deductionInStockUnit)->toEqualWithDelta(0.85, 0.000001);
});
