<?php

namespace App\Services;

use App\Models\InventoryItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class InventoryAlertService
{
    private const DEFAULT_LOW_STOCK_THRESHOLD = 5;

    private ?bool $hasInventoryTable = null;
    private ?bool $hasExpiryDateColumn = null;
    private ?bool $hasMinStockColumn = null;
    private ?Collection $inventoryItems = null;
    private ?Carbon $today = null;
    private ?Carbon $expiryWindowEnd = null;

    public function getLowStocks(): Collection
    {
        if (!$this->canQueryInventory()) {
            return collect();
        }

        return $this->getInventoryItems()
            ->filter(fn (InventoryItem $item) => $this->isLowStock($item))
            ->values();
    }

    public function getOutOfStocks(): Collection
    {
        if (!$this->canQueryInventory()) {
            return collect();
        }

        return $this->getInventoryItems()
            ->filter(fn (InventoryItem $item) => $this->isOutOfStock($item))
            ->values();
    }

    public function getExpiringSoon(): Collection
    {
        if (!$this->canQueryInventory() || !$this->hasExpiryDateColumn()) {
            return collect();
        }

        return $this->getInventoryItems()
            ->filter(fn (InventoryItem $item) => $this->isExpiringSoon($item))
            ->values();
    }

    public function getAlerts(): Collection
    {
        if (!$this->canQueryInventory()) {
            return collect();
        }

        return $this->getInventoryItems()
            ->map(fn (InventoryItem $item) => $this->formatAlert($item))
            ->filter()
            ->values();
    }

    public function getWarningCount(): int
    {
        return $this->getAlerts()->count();
    }

    private function formatAlert(InventoryItem $item): ?array
    {
        $status = null;

        if ($this->isOutOfStock($item)) {
            $status = 'out_of_stock';
        } elseif ($this->isLowStock($item)) {
            $status = 'low_stock';
        } elseif ($this->hasExpiryDateColumn() && $this->isExpiringSoon($item)) {
            $status = 'expiring';
        }

        if ($status === null) {
            return null;
        }

        return [
            'id' => $item->id,
            'name' => $item->name,
            'current_stock' => $item->qty,
            'min_stock' => $this->resolveLowStockThreshold($item),
            'expiry_date' => $this->hasExpiryDateColumn() && !empty($item->expiry_date)
                ? Carbon::parse($item->expiry_date)->toDateString()
                : null,
            'status' => $status,
        ];
    }

    private function isOutOfStock(InventoryItem $item): bool
    {
        return (float) $item->qty <= 0;
    }

    private function isLowStock(InventoryItem $item): bool
    {
        $currentStock = (float) $item->qty;

        return $currentStock > 0
            && $currentStock <= $this->resolveLowStockThreshold($item);
    }

    private function isExpiringSoon(InventoryItem $item): bool
    {
        if (empty($item->expiry_date)) {
            return false;
        }

        $expiryDate = Carbon::parse($item->expiry_date);

        return $expiryDate->between($this->today(), $this->expiryWindowEnd(), true);
    }

    private function resolveLowStockThreshold(InventoryItem $item): int|float
    {
        if ($this->hasMinStockColumn()) {
            $minStock = $item->getAttribute('min_stock');

            if (is_numeric($minStock)) {
                return (float) $minStock;
            }
        }

        return self::DEFAULT_LOW_STOCK_THRESHOLD;
    }

    private function canQueryInventory(): bool
    {
        return $this->hasInventoryTable();
    }

    private function hasInventoryTable(): bool
    {
        if ($this->hasInventoryTable === null) {
            $this->hasInventoryTable = Schema::hasTable('inventory_items');
        }

        return $this->hasInventoryTable;
    }

    private function hasExpiryDateColumn(): bool
    {
        if ($this->hasExpiryDateColumn === null) {
            $this->hasExpiryDateColumn = $this->hasInventoryTable()
                && Schema::hasColumn('inventory_items', 'expiry_date');
        }

        return $this->hasExpiryDateColumn;
    }

    private function hasMinStockColumn(): bool
    {
        if ($this->hasMinStockColumn === null) {
            $this->hasMinStockColumn = $this->hasInventoryTable()
                && Schema::hasColumn('inventory_items', 'min_stock');
        }

        return $this->hasMinStockColumn;
    }

    private function getInventoryItems(): Collection
    {
        if ($this->inventoryItems === null) {
            $this->inventoryItems = $this->hasInventoryTable()
                ? InventoryItem::query()->orderBy('name')->get()
                : collect();
        }

        return $this->inventoryItems;
    }

    private function today(): Carbon
    {
        if ($this->today === null) {
            $this->today = Carbon::today();
        }

        return $this->today->copy();
    }

    private function expiryWindowEnd(): Carbon
    {
        if ($this->expiryWindowEnd === null) {
            $this->expiryWindowEnd = Carbon::today()->addDays(7);
        }

        return $this->expiryWindowEnd->copy();
    }
}
