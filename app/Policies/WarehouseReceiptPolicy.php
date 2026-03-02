<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseReceipt;

class WarehouseReceiptPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, WarehouseReceipt $warehouseReceipt): bool
    {
        return $warehouseReceipt->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }
}
