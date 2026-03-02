<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = Supplier::withCount('containers')
            ->when($request->input('search'), fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Supplier::class);

        return Inertia::render('Suppliers/Create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')->with('success', __('Supplier created.'));
    }

    public function show(Supplier $supplier): Response
    {
        $this->authorize('view', $supplier);

        $supplier->load(['containers' => fn ($q) => $q->withSum('supplierPayments', 'amount')->with('supplierPayments')]);

        $supplierTotalCost = $supplier->containers->sum('total_cost');
        $supplierPaidAmount = $supplier->containers->sum(fn ($c) => (float) ($c->supplier_payments_sum_amount ?? 0));
        $supplierRemainingAmount = max(0, $supplierTotalCost - $supplierPaidAmount);

        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
            'supplier_total_cost' => $supplierTotalCost,
            'supplier_paid_amount' => $supplierPaidAmount,
            'supplier_remaining_amount' => $supplierRemainingAmount,
        ]);
    }

    public function edit(Supplier $supplier): Response
    {
        $this->authorize('update', $supplier);

        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.show', $supplier)->with('success', __('Supplier updated.'));
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete', $supplier);

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', __('Supplier deleted.'));
    }
}
