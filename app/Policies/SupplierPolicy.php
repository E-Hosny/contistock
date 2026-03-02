<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $supplier->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $supplier->tenant_id === $user->tenant_id;
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $supplier->tenant_id === $user->tenant_id;
    }
}
