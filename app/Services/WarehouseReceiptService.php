<?php

namespace App\Services;

use App\Models\Container;
use App\Models\ReceiptItem;
use App\Models\StockMovement;
use App\Models\WarehouseReceipt;
use Illuminate\Support\Facades\DB;

class WarehouseReceiptService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Create warehouse receipt with items and stock movements (in).
     */
    public function createReceipt(Container $container, array $attributes, array $items): WarehouseReceipt
    {
        return DB::transaction(function () use ($container, $attributes, $items) {
            $receipt = WarehouseReceipt::create([
                'tenant_id' => \current_tenant_id(),
                'container_id' => $container->id,
                'received_by' => auth()->id(),
                'receipt_date' => $attributes['receipt_date'] ?? now()->toDateString(),
                'notes' => $attributes['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $receiptItem = ReceiptItem::create([
                    'tenant_id' => \current_tenant_id(),
                    'warehouse_receipt_id' => $receipt->id,
                    'container_id' => $container->id,
                    'product_id' => $item['product_id'],
                    'qty_received' => $item['qty_received'],
                    'buy_price' => $item['buy_price'],
                    'sale_price' => $item['sale_price'],
                ]);

                StockMovement::create([
                    'tenant_id' => \current_tenant_id(),
                    'product_id' => $item['product_id'],
                    'container_id' => $container->id,
                    'qty' => $item['qty_received'],
                    'type' => StockMovement::TYPE_IN,
                    'ref_type' => 'warehouse_receipt',
                    'ref_id' => $receipt->id,
                    'movement_date' => $receipt->receipt_date,
                ]);
            }

            $container->update(['status' => 'received_to_warehouse']);

            return $receipt->load('receiptItems');
        });
    }
}
