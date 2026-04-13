<?php

namespace App\Services;

use App\Models\ReceiptItem;
use App\Models\StockMovement;

class StockService
{
    /**
     * Get current available stock for a product (optionally for a specific container).
     */
    public function availableQty(int $productId, ?int $containerId = null): float
    {
        $builder = function () use ($productId, $containerId) {
            $q = StockMovement::where('product_id', $productId);
            if ($containerId !== null) {
                $q->where('container_id', $containerId);
            }

            return $q;
        };

        $in = (float) $builder()->where('type', StockMovement::TYPE_IN)->sum('qty');
        $out = (float) $builder()->where('type', StockMovement::TYPE_OUT)->sum('qty');
        $adjust = (float) $builder()->where('type', StockMovement::TYPE_ADJUST)->sum('qty');

        return $in - $out + $adjust;
    }

    /**
     * Check if we can fulfill a quantity (unless allow negative stock is on).
     */
    public function canFulfill(int $productId, float $qty, ?int $containerId = null): bool
    {
        if ($this->allowNegativeStock()) {
            return true;
        }

        return $this->availableQty($productId, $containerId) >= $qty;
    }

    public function allowNegativeStock(): bool
    {
        $setting = \App\Models\Setting::where('key', 'allow_negative_stock')->first();

        return $setting && in_array(strtolower($setting->value ?? ''), ['1', 'true', 'yes'], true);
    }

    /**
     * Product IDs that have at least one purchase/receipt line on the container (pending or received).
     *
     * @return list<int>
     */
    public function productIdsLinkedToContainer(int $containerId): array
    {
        return ReceiptItem::query()
            ->where('container_id', $containerId)
            ->distinct()
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
}
