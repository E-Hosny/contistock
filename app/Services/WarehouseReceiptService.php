<?php

namespace App\Services;

use App\Models\Container;
use App\Models\ReceiptItem;
use App\Models\StockMovement;
use App\Models\WarehouseReceipt;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WarehouseReceiptService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Link pending container purchase lines to a new warehouse receipt and record stock in.
     */
    public function finalizePlannedReceipt(Container $container, array $attributes): WarehouseReceipt
    {
        return DB::transaction(function () use ($container, $attributes) {
            if ($container->status === 'received_to_warehouse') {
                throw new RuntimeException(__('This container is already received into the warehouse.'));
            }

            $pending = ReceiptItem::query()
                ->where('container_id', $container->id)
                ->whereNull('warehouse_receipt_id')
                ->orderBy('id')
                ->get();

            if ($pending->isEmpty()) {
                throw new RuntimeException(__('Add purchase lines on the container page before receiving to the warehouse.'));
            }

            $receipt = WarehouseReceipt::create([
                'tenant_id' => \current_tenant_id(),
                'container_id' => $container->id,
                'received_by' => auth()->id(),
                'receipt_date' => $attributes['receipt_date'] ?? now()->toDateString(),
                'notes' => $attributes['notes'] ?? null,
            ]);

            foreach ($pending as $receiptItem) {
                $receiptItem->update(['warehouse_receipt_id' => $receipt->id]);

                StockMovement::create([
                    'tenant_id' => \current_tenant_id(),
                    'product_id' => $receiptItem->product_id,
                    'container_id' => $container->id,
                    'qty' => (float) $receiptItem->qty_received,
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

    /**
     * Create warehouse receipt with new line items (e.g. tests / legacy).
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
                ReceiptItem::create([
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
