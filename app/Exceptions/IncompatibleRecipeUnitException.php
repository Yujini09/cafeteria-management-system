<?php

namespace App\Exceptions;

use RuntimeException;

class IncompatibleRecipeUnitException extends RuntimeException
{
    /**
     * @param array<int, array{
     *   context?: string,
     *   menu_item?: string,
     *   ingredient?: string,
     *   recipe_unit?: string,
     *   stock_unit?: string
     * }> $violations
     */
    public function __construct(private readonly array $violations)
    {
        parent::__construct('Incompatible recipe and inventory units detected.');
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function violations(): array
    {
        return $this->violations;
    }

    /**
     * @return array<int, string>
     */
    public function messages(): array
    {
        return array_map(function (array $violation): string {
            $context = trim((string) ($violation['context'] ?? ''));
            $menuItem = trim((string) ($violation['menu_item'] ?? 'Unknown menu item'));
            $ingredient = trim((string) ($violation['ingredient'] ?? 'Unknown ingredient'));
            $recipeUnit = trim((string) ($violation['recipe_unit'] ?? 'unknown'));
            $stockUnit = trim((string) ($violation['stock_unit'] ?? 'unknown'));

            $prefix = $context !== '' ? "{$context}: " : '';

            return "{$prefix}Unit mismatch for ingredient \"{$ingredient}\" in \"{$menuItem}\". Recipe unit: {$recipeUnit}; stock unit: {$stockUnit}.";
        }, $this->violations);
    }
}
