<?php

namespace App\Policies;

use App\Models\SupplierPayment;
use App\Models\User;

class SupplierPaymentPolicy
{
    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function delete(User $user, SupplierPayment $supplierPayment): bool
    {
        return $supplierPayment->tenant_id === $user->tenant_id;
    }
}
