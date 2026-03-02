<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $slug = fake()->unique()->slug(2);

        return [
            'name' => fake()->company(),
            'slug' => $slug,
            'settings' => null,
            'is_active' => true,
        ];
    }
}
