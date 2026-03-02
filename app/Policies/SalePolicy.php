<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, Sale $sale): bool
    {
        return $sale->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function update(User $user, Sale $sale): bool
    {
        return $sale->tenant_id === $user->tenant_id;
    }

    public function delete(User $user, Sale $sale): bool
    {
        return $sale->tenant_id === $user->tenant_id;
    }
}
