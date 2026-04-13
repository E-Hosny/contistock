<?php

namespace App\Services;

use App\Models\Container;

class ContainerCostService
{
    /**
     * Recalculate container total_cost from purchase lines (qty × buy) + extra expenses.
     */
    public function refreshTotalCost(Container $container): void
    {
        $purchaseTotal = (float) $container->receiptItems()
            ->get()
            ->sum(fn ($line) => (float) $line->qty_received * (float) $line->buy_price);

        $expensesTotal = (float) $container->expenses()->sum('amount');

        $container->updateQuietly([
            'total_cost' => round($purchaseTotal + $expensesTotal, 2),
        ]);
    }
}
