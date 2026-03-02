<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContainerRequest;
use App\Http\Requests\UpdateContainerRequest;
use App\Models\Container;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContainerController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Container::class);

        $query = Container::with('supplier');
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%'.$request->input('search').'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $containers = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        foreach ($containers as $c) {
            $c->append(['paid_amount', 'remaining_amount']);
        }

        return Inertia::render('Containers/Index', [
            'containers' => $containers,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Container::class);

        return Inertia::render('Containers/Create', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreContainerRequest $request): RedirectResponse
    {
        $container = Container::create($request->validated());

        return redirect()->route('containers.show', $container)->with('success', __('Container created.'));
    }

    public function show(Container $container): Response
    {
        $this->authorize('view', $container);

        $container->load([
            'supplier',
            'supplierPayments',
            'warehouseReceipts.receiptItems.product',
        ]);
        $container->append(['paid_amount', 'remaining_amount']);

        return Inertia::render('Containers/Show', [
            'container' => $container,
        ]);
    }

    public function edit(Container $container): Response
    {
        $this->authorize('update', $container);

        return Inertia::render('Containers/Edit', [
            'container' => $container,
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateContainerRequest $request, Container $container): RedirectResponse
    {
        $container->update($request->validated());

        return redirect()->route('containers.show', $container)->with('success', __('Container updated.'));
    }

    public function destroy(Container $container): RedirectResponse
    {
        $this->authorize('delete', $container);

        $container->delete();

        return redirect()->route('containers.index')->with('success', __('Container deleted.'));
    }
}
