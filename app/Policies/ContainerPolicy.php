<?php

namespace App\Policies;

use App\Models\Container;
use App\Models\User;

class ContainerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, Container $container): bool
    {
        return $container->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function update(User $user, Container $container): bool
    {
        return $container->tenant_id === $user->tenant_id;
    }

    public function delete(User $user, Container $container): bool
    {
        return $container->tenant_id === $user->tenant_id;
    }
}
