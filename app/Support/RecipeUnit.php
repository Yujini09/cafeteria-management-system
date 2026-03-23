<?php

namespace App\Support;

class RecipeUnit
{
    public const RECIPE_UNITS = ['ml', 'liters', 'g', 'kgs', 'pieces', 'packs'];

    private const UNIT_ALIASES = [
        'ml' => 'ml',
        'milliliter' => 'ml',
        'milliliters' => 'ml',
        'millilitre' => 'ml',
        'millilitres' => 'ml',
        'liter' => 'liters',
        'liters' => 'liters',
        'litre' => 'liters',
        'litres' => 'liters',
        'l' => 'liters',
        'g' => 'g',
        'gram' => 'g',
        'grams' => 'g',
        'kg' => 'kgs',
        'kgs' => 'kgs',
        'kilogram' => 'kgs',
        'kilograms' => 'kgs',
        'pc' => 'pieces',
        'pcs' => 'pieces',
        'piece' => 'pieces',
        'pieces' => 'pieces',
        'pack' => 'packs',
        'packs' => 'packs',
    ];

    public static function normalize(?string $unit): ?string
    {
        if ($unit === null) {
            return null;
        }

        $normalized = strtolower(trim($unit));

        if ($normalized === '') {
            return null;
        }

        return self::UNIT_ALIASES[$normalized] ?? $normalized;
    }

    public static function display(?string $unit): string
    {
        return self::normalize($unit) ?? '';
    }

    public static function requiresWholeQuantity(?string $unit): bool
    {
        $normalized = self::normalize($unit);

        return in_array($normalized, ['pieces', 'packs'], true);
    }

    public static function formatStockQuantity(mixed $quantity, ?string $unit): string
    {
        $numeric = is_numeric($quantity) ? (float) $quantity : 0.0;
        $precision = self::requiresWholeQuantity($unit) ? 0 : 2;

        return number_format($numeric, $precision);
    }

    public static function isAllowedRecipeUnit(?string $unit): bool
    {
        $normalized = self::normalize($unit);

        return $normalized !== null && in_array($normalized, self::RECIPE_UNITS, true);
    }

    public static function areCompatible(?string $recipeUnit, ?string $stockUnit): bool
    {
        $normalizedRecipeUnit = self::normalize($recipeUnit);
        $normalizedStockUnit = self::normalize($stockUnit);

        if ($normalizedRecipeUnit === null || $normalizedStockUnit === null) {
            return false;
        }

        $recipeFamily = self::family($normalizedRecipeUnit);
        $stockFamily = self::family($normalizedStockUnit);

        if ($recipeFamily === null || $recipeFamily !== $stockFamily) {
            return false;
        }

        if ($recipeFamily === 'pack') {
            return $normalizedRecipeUnit === 'packs' && $normalizedStockUnit === 'packs';
        }

        return true;
    }

    public static function convertToStockUnit(float $quantity, ?string $recipeUnit, ?string $stockUnit): ?float
    {
        $normalizedRecipeUnit = self::normalize($recipeUnit);
        $normalizedStockUnit = self::normalize($stockUnit);

        if (!self::areCompatible($normalizedRecipeUnit, $normalizedStockUnit)) {
            return null;
        }

        if ($normalizedRecipeUnit === $normalizedStockUnit) {
            return $quantity;
        }

        if ($normalizedStockUnit === 'liters' && $normalizedRecipeUnit === 'ml') {
            return $quantity / 1000;
        }

        if ($normalizedStockUnit === 'ml' && $normalizedRecipeUnit === 'liters') {
            return $quantity * 1000;
        }

        if ($normalizedStockUnit === 'kgs' && $normalizedRecipeUnit === 'g') {
            return $quantity / 1000;
        }

        if ($normalizedStockUnit === 'g' && $normalizedRecipeUnit === 'kgs') {
            return $quantity * 1000;
        }

        return $quantity;
    }

    private static function family(string $unit): ?string
    {
        return match ($unit) {
            'ml', 'liters' => 'volume',
            'g', 'kgs' => 'weight',
            'pieces' => 'count',
            'packs' => 'pack',
            default => null,
        };
    }
}
