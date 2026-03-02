<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tenant;
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

        Container::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'product_name' => 'Sample Container',
                'invoice_ref' => 'INV-DEMO-001',
            ],
            [
                'tenant_id' => $tenant->id,
                'supplier_id' => $supplier->id,
                'product_name' => 'Sample Container',
                'total_cost' => 1000,
                'purchase_date' => now(),
                'invoice_ref' => 'INV-DEMO-001',
                'status' => 'draft',
            ]
        );
    }
}
