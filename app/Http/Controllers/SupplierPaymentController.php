<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierPaymentRequest;
use App\Models\Container;
use App\Models\SupplierPayment;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SupplierPaymentController extends Controller
{
    public function index(Container $container): Response
    {
        $this->authorize('view', $container);

        $container->load('supplierPayments');
        $container->append(['paid_amount', 'remaining_amount']);

        return Inertia::render('Payments/SupplierPayments', [
            'container' => $container,
        ]);
    }

    public function store(StoreSupplierPaymentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $container = Container::findOrFail($validated['container_id']);
        $this->authorize('view', $container);

        SupplierPayment::create([
            'tenant_id' => \current_tenant_id(),
            'container_id' => $validated['container_id'],
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'method' => $validated['method'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', __('Payment recorded.'));
    }

    public function destroy(SupplierPayment $supplierPayment): RedirectResponse
    {
        $this->authorize('delete', $supplierPayment);

        $containerId = $supplierPayment->container_id;
        $supplierPayment->delete();

        return redirect()->route('containers.purchases', $containerId)->with('success', __('Payment deleted.'));
    }
}
