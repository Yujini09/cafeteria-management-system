<?php

use App\Support\RecipeUnit;

it('normalizes canonical units and accepted aliases', function () {
    expect(RecipeUnit::normalize('pc'))->toBe('pieces');
    expect(RecipeUnit::normalize('pcs'))->toBe('pieces');
    expect(RecipeUnit::normalize('piece'))->toBe('pieces');
    expect(RecipeUnit::normalize('kg'))->toBe('kgs');
    expect(RecipeUnit::normalize('liter'))->toBe('liters');
    expect(RecipeUnit::normalize('litre'))->toBe('liters');
    expect(RecipeUnit::normalize('l'))->toBe('liters');
    expect(RecipeUnit::normalize('gram'))->toBe('g');
    expect(RecipeUnit::normalize('grams'))->toBe('g');
    expect(RecipeUnit::normalize('pack'))->toBe('packs');
});

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

    expect(RecipeUnit::convertToStockUnit(1.5, 'liters', 'ml'))->toEqualWithDelta(1500.0, 0.000001);
    expect(RecipeUnit::convertToStockUnit(500.0, 'g', 'kgs'))->toEqualWithDelta(0.5, 0.000001);
    expect(RecipeUnit::convertToStockUnit(1.25, 'kgs', 'g'))->toEqualWithDelta(1250.0, 0.000001);
});

it('returns null for incompatible cross-category conversion', function () {
    expect(RecipeUnit::convertToStockUnit(1.0, 'ml', 'g'))->toBeNull();
    expect(RecipeUnit::convertToStockUnit(2.0, 'packs', 'pieces'))->toBeNull();
});
