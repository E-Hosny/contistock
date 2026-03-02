<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseReceiptRequest;
use App\Models\Container;
use App\Models\Product;
use App\Models\WarehouseReceipt;
use App\Services\WarehouseReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseReceiptController extends Controller
{
    public function __construct(
        protected WarehouseReceiptService $warehouseReceiptService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', WarehouseReceipt::class);

        $receipts = WarehouseReceipt::with(['container', 'receivedByUser'])
            ->when($request->input('container_id'), fn ($q, $v) => $q->where('container_id', $v))
            ->orderByDesc('receipt_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('WarehouseReceipts/Index', [
            'receipts' => $receipts,
            'filters' => $request->only('container_id'),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', WarehouseReceipt::class);

        $containers = Container::whereIn('status', ['draft', 'purchased'])
            ->orderBy('product_name')
            ->get(['id', 'product_name', 'supplier_id']);
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);

        return Inertia::render('WarehouseReceipts/Create', [
            'containers' => $containers,
            'products' => $products,
            'preselectedContainerId' => $request->input('container_id'),
        ]);
    }

    public function store(StoreWarehouseReceiptRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $container = Container::findOrFail($validated['container_id']);
        $this->authorize('view', $container);

        $this->warehouseReceiptService->createReceipt(
            $container,
            [
                'receipt_date' => $validated['receipt_date'],
                'notes' => $validated['notes'] ?? null,
            ],
            $validated['items']
        );

        return redirect()->route('warehouse-receipts.index')->with('success', __('Warehouse receipt created.'));
    }

    public function show(WarehouseReceipt $warehouseReceipt): Response
    {
        $this->authorize('view', $warehouseReceipt);

        $warehouseReceipt->load(['container.supplier', 'receiptItems.product', 'receivedByUser']);

        return Inertia::render('WarehouseReceipts/Show', [
            'receipt' => $warehouseReceipt,
        ]);
    }
}
