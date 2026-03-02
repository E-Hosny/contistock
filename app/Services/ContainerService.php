<?php

namespace App\Services;

use App\Models\Container;

class ContainerService
{
    /**
     * Paid amount for a container (sum of supplier payments).
     */
    public function paidAmount(Container $container): float
    {
        return (float) $container->supplierPayments()->sum('amount');
    }

    /**
     * Remaining amount to pay for a container.
     */
    public function remainingAmount(Container $container): float
    {
        return max(0, (float) $container->total_cost - $this->paidAmount($container));
    }
}
