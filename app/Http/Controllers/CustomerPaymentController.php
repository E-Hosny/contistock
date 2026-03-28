<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerPaymentRequest;
use App\Models\CustomerPayment;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CustomerPaymentController extends Controller
{
    public function index(Sale $sale): Response
    {
        $this->authorize('view', $sale);

        $sale->load('customerPayments');
        $sale->append(['paid_amount', 'remaining_amount']);

        return Inertia::render('Payments/CustomerPayments', [
            'sale' => $sale,
        ]);
    }

    public function store(StoreCustomerPaymentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $sale = Sale::findOrFail($validated['sale_id']);
        $this->authorize('view', $sale);

        CustomerPayment::create([
            'tenant_id' => \current_tenant_id(),
            'sale_id' => $validated['sale_id'],
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'method' => $validated['method'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', __('Payment recorded.'));
    }

    public function destroy(CustomerPayment $customerPayment): RedirectResponse
    {
        $this->authorize('delete', $customerPayment);

        $saleId = $customerPayment->sale_id;
        $customerPayment->delete();

        return redirect()->route('sales.customer-payments.index', $saleId)->with('success', __('Payment deleted.'));
    }
}
