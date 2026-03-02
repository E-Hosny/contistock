<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        $tenantId = $request->user()->tenant_id;

        if ($tenantId === null) {
            abort(403, 'User must belong to a tenant.');
        }

        app()->instance('current_tenant_id', $tenantId);

        return $next($request);
    }
}
