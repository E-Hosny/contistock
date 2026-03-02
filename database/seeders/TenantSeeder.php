<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            ['slug' => 'demo'],
            [
                'name' => 'Demo Tenant',
                'slug' => 'demo',
                'settings' => null,
                'is_active' => true,
            ]
        );

        Tenant::firstOrCreate(
            ['slug' => 'test'],
            [
                'name' => 'Test Tenant',
                'slug' => 'test',
                'settings' => null,
                'is_active' => true,
            ]
        );
    }
}
