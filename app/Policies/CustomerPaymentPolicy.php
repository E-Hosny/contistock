<?php

namespace App\Policies;

use App\Models\CustomerPayment;
use App\Models\User;

class CustomerPaymentPolicy
{
    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function delete(User $user, CustomerPayment $customerPayment): bool
    {
        return $customerPayment->tenant_id === $user->tenant_id;
    }
}
