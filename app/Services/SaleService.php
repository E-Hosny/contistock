<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Paid amount for a sale (sum of customer payments).
     */
    public function paidAmount(Sale $sale): float
    {
        return (float) $sale->customerPayments()->sum('amount');
    }

    /**
     * Remaining amount to collect for a sale.
     */
    public function remainingAmount(Sale $sale): float
    {
        return max(0, (float) $sale->total - $this->paidAmount($sale));
    }

    /**
     * Confirm sale: create stock movements (out) for each item.
     */
    public function confirm(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->saleItems as $item) {
                if (! $this->stockService->canFulfill(
                    (int) $item->product_id,
                    (float) $item->qty,
                    (int) $item->container_id
                )) {
                    throw new \InvalidArgumentException(
                        "Insufficient stock for product #{$item->product_id}. Available: " .
                        $this->stockService->availableQty((int) $item->product_id, (int) $item->container_id)
                    );
                }

                StockMovement::create([
                    'tenant_id' => \current_tenant_id(),
                    'product_id' => $item->product_id,
                    'container_id' => $item->container_id,
                    'qty' => $item->qty,
                    'type' => StockMovement::TYPE_OUT,
                    'ref_type' => 'sale',
                    'ref_id' => $sale->id,
                    'movement_date' => $sale->sale_date,
                ]);
            }

            $sale->update(['status' => 'confirmed']);
        });
    }

    /**
     * Cancel sale (status only; no automatic stock reversal).
     */
    public function cancel(Sale $sale): void
    {
        $sale->update(['status' => 'cancelled']);
    }
}
