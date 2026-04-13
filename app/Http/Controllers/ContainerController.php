<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContainerRequest;
use App\Http\Requests\UpdateContainerRequest;
use App\Models\Container;
use App\Models\Product;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use App\Services\ContainerSalesSummaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContainerController extends Controller
{
    public function __construct(
        protected ContainerSalesSummaryService $containerSalesSummaryService
    ) {}

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
        $data = $request->validated();
        $data['total_cost'] = $data['total_cost'] ?? 0;
        $container = Container::create($data);

        return redirect()->route('containers.show', $container)->with('success', __('Container created.'));
    }

    public function show(Container $container): Response
    {
        $this->authorize('view', $container);

        $container->load('supplier');
        $container->append(['paid_amount', 'remaining_amount']);

        $salesWorkspace = $this->containerSalesSummaryService->forContainer($container);

        return Inertia::render('Containers/Show', [
            'container' => $container,
            'purchasesCard' => [
                'total' => (float) $container->total_cost,
                'paid' => (float) $container->paid_amount,
                'remaining' => (float) $container->remaining_amount,
            ],
            'salesCard' => $salesWorkspace['summary'],
            'canReceiveToWarehouse' => $container->canEditPurchasePlan()
                && ReceiptItem::query()
                    ->where('container_id', $container->id)
                    ->whereNull('warehouse_receipt_id')
                    ->exists(),
        ]);
    }

    public function purchases(Container $container): Response
    {
        $this->authorize('view', $container);

        return Inertia::render('Containers/Purchases', $this->purchasesWorkspacePayload($container));
    }

    public function sales(Container $container): Response
    {
        $this->authorize('view', $container);

        return Inertia::render('Containers/Sales', $this->salesWorkspacePayload($container));
    }

    /**
     * @return array<string, mixed>
     */
    protected function purchasesWorkspacePayload(Container $container): array
    {
        $container->load([
            'supplier',
            'supplierPayments',
            'warehouseReceipts.receiptItems.product',
            'expenses',
        ]);
        $container->append(['paid_amount', 'remaining_amount']);

        $pendingReceiptItems = ReceiptItem::query()
            ->where('container_id', $container->id)
            ->whereNull('warehouse_receipt_id')
            ->with('product')
            ->orderBy('id')
            ->get();

        $receivedReceiptItems = ReceiptItem::query()
            ->where('container_id', $container->id)
            ->whereNotNull('warehouse_receipt_id')
            ->with(['product', 'warehouseReceipt'])
            ->orderByDesc('id')
            ->get();

        $purchaseSubtotal = (float) $container->receiptItems()
            ->get()
            ->sum(fn ($line) => (float) $line->qty_received * (float) $line->buy_price);
        $expensesSubtotal = (float) $container->expenses()->sum('amount');

        return [
            'container' => $container,
            'pendingReceiptItems' => $pendingReceiptItems,
            'receivedReceiptItems' => $receivedReceiptItems,
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku']),
            'purchaseSubtotal' => round($purchaseSubtotal, 2),
            'expensesSubtotal' => round($expensesSubtotal, 2),
            'canEditPurchasePlan' => $container->canEditPurchasePlan(),
            'canReceiveToWarehouse' => $container->canEditPurchasePlan()
                && $pendingReceiptItems->isNotEmpty(),
            'purchasesCard' => [
                'total' => (float) $container->total_cost,
                'paid' => (float) $container->paid_amount,
                'remaining' => (float) $container->remaining_amount,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function salesWorkspacePayload(Container $container): array
    {
        $container->load('supplier');
        $container->append(['paid_amount', 'remaining_amount']);

        $salesWorkspace = $this->containerSalesSummaryService->forContainer($container);

        return [
            'container' => $container,
            'salesCard' => $salesWorkspace['summary'],
            'containerSales' => $salesWorkspace['sales'],
            'customerPayments' => $salesWorkspace['customerPayments'],
            'payableSales' => $salesWorkspace['payableSales'],
        ];
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
