<?php

if (! function_exists('current_tenant_id')) {
    function current_tenant_id(): ?int
    {
        return app()->bound('current_tenant_id') ? app('current_tenant_id') : null;
    }
}
