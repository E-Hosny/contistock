<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'demo')->first();
        if (! $tenant) {
            return;
        }

        User::firstOrCreate(
            [
                'email' => 'admin@contistock.demo',
                'tenant_id' => $tenant->id,
            ],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Admin Demo',
                'email' => 'admin@contistock.demo',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
