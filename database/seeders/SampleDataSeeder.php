<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Services\ContainerCostService;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'demo')->first();
        if (! $tenant) {
            return;
        }

        app()->instance('current_tenant_id', $tenant->id);

        $supplier = Supplier::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'Sample Supplier',
            ],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Sample Supplier',
                'phone' => '+1234567890',
                'email' => 'supplier@example.com',
                'is_active' => true,
            ]
        );

        $customer = Customer::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'Sample Customer',
            ],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Sample Customer',
                'phone' => '+0987654321',
                'status' => 'active',
            ]
        );

        Product::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'sku' => 'SKU-001',
            ],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Sample Product',
                'sku' => 'SKU-001',
                'unit' => 'piece',
            ]
        );

        $container = Container::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'product_name' => 'Sample Container',
                'invoice_ref' => 'INV-DEMO-001',
            ],
            [
                'tenant_id' => $tenant->id,
                'supplier_id' => $supplier->id,
                'product_name' => 'Sample Container',
                'total_cost' => 0,
                'purchase_date' => now(),
                'invoice_ref' => 'INV-DEMO-001',
                'status' => 'draft',
            ]
        );

        $product = Product::where('tenant_id', $tenant->id)->where('sku', 'SKU-001')->first();
        if ($product && ! ReceiptItem::where('container_id', $container->id)->exists()) {
            ReceiptItem::create([
                'tenant_id' => $tenant->id,
                'warehouse_receipt_id' => null,
                'container_id' => $container->id,
                'product_id' => $product->id,
                'qty_received' => 100,
                'buy_price' => 10,
                'sale_price' => 15,
            ]);
        }

        app(ContainerCostService::class)->refreshTotalCost($container->fresh());
    }
}
