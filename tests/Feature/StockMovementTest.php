<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Services\StockService;
use App\Services\WarehouseReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_receipt_creates_stock_movements_in(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $supplier = Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => 'S1',
            'is_active' => true,
        ]);
        $container = Container::create([
            'tenant_id' => $tenant->id,
            'supplier_id' => $supplier->id,
            'product_name' => 'C1',
            'total_cost' => 100,
            'status' => 'purchased',
        ]);
        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'P1',
            'sku' => 'SKU1',
        ]);

        $this->actingAs($user);
        app()->instance('current_tenant_id', $tenant->id);

        $service = app(WarehouseReceiptService::class);
        $service->createReceipt($container, [
            'receipt_date' => now()->toDateString(),
        ], [
            ['product_id' => $product->id, 'qty_received' => 10, 'buy_price' => 5, 'sale_price' => 8],
        ]);

        $this->assertDatabaseCount('stock_movements', 1);
        $movement = StockMovement::first();
        $this->assertSame(StockMovement::TYPE_IN, $movement->type);
        $this->assertSame(10.0, (float) $movement->qty);

        $stockService = app(StockService::class);
        $available = $stockService->availableQty((int) $product->id, (int) $container->id);
        $this->assertSame(10.0, $available);
    }
}
