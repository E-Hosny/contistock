<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_supplier_from_another_tenant(): void
    {
        $tenantA = Tenant::factory()->create(['slug' => 'tenant-a']);
        $tenantB = Tenant::factory()->create(['slug' => 'tenant-b']);

        $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
        $supplierB = Supplier::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Supplier B',
            'is_active' => true,
        ]);

        $response = $this->actingAs($userA)->get(route('suppliers.show', $supplierB));

        $response->assertStatus(403);
    }

    public function test_user_can_view_own_tenant_supplier(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $supplier = Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => 'My Supplier',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('suppliers.show', $supplier));

        $response->assertOk();
    }

    public function test_user_cannot_update_container_from_another_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
        $supplierB = Supplier::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Supplier B',
            'is_active' => true,
        ]);
        $containerB = Container::create([
            'tenant_id' => $tenantB->id,
            'supplier_id' => $supplierB->id,
            'product_name' => 'Container B',
            'total_cost' => 100,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($userA)->put(route('containers.update', $containerB), [
            'supplier_id' => $supplierB->id,
            'product_name' => 'Hacked',
            'total_cost' => 100,
            'status' => 'draft',
        ]);

        $response->assertStatus(403);
        $containerB->refresh();
        $this->assertSame('Container B', $containerB->product_name);
    }
}
