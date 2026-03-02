<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReceiptItem;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WarehouseReceipt;
use App\Services\SaleService;
use App\Services\WarehouseReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleConfirmTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_sale_creates_stock_movements_out(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $supplier = Supplier::create(['tenant_id' => $tenant->id, 'name' => 'S1', 'is_active' => true]);
        $container = Container::create([
            'tenant_id' => $tenant->id,
            'supplier_id' => $supplier->id,
            'product_name' => 'C1',
            'total_cost' => 100,
            'status' => 'received_to_warehouse',
        ]);
        $product = Product::create(['tenant_id' => $tenant->id, 'name' => 'P1', 'sku' => 'SKU1']);
        $customer = Customer::create(['tenant_id' => $tenant->id, 'name' => 'C1', 'status' => 'active']);

        app()->instance('current_tenant_id', $tenant->id);
        $wrService = app(WarehouseReceiptService::class);
        $wrService->createReceipt($container, ['receipt_date' => now()->toDateString()], [
            ['product_id' => $product->id, 'qty_received' => 10, 'buy_price' => 5, 'sale_price' => 8],
        ]);

        $sale = Sale::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'sale_date' => now(),
            'status' => 'draft',
            'subtotal' => 80,
            'discount' => 0,
            'total' => 80,
        ]);
        $sale->saleItems()->create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'container_id' => $container->id,
            'qty' => 3,
            'unit_price' => 8,
            'buy_price_snapshot' => 5,
            'line_total' => 24,
            'profit_line' => 9,
        ]);

        $beforeCount = StockMovement::count();
        $saleService = app(SaleService::class);
        $saleService->confirm($sale);

        $this->assertSame($beforeCount + 1, StockMovement::count());
        $out = StockMovement::where('type', StockMovement::TYPE_OUT)->latest()->first();
        $this->assertNotNull($out);
        $this->assertSame(3.0, (float) $out->qty);
        $sale->refresh();
        $this->assertSame('confirmed', $sale->status);
    }
}
