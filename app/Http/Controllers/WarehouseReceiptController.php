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
            ->orderByDesc('receipt_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('WarehouseReceipts/Index', [
            'receipts' => $receipts,
            'filters' => $request->only([]),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', WarehouseReceipt::class);

        $preselectedContainerId = $request->query('container_id');

        return Inertia::render('WarehouseReceipts/Create', [
            'containers' => Container::orderBy('product_name')->get(['id', 'product_name']),
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku']),
            'preselectedContainerId' => $preselectedContainerId ? (int) $preselectedContainerId : null,
        ]);
    }

    public function store(StoreWarehouseReceiptRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $container = Container::findOrFail($validated['container_id']);

        $this->authorize('create', WarehouseReceipt::class);

        $items = array_map(fn ($item) => [
            'product_id' => $item['product_id'],
            'qty_received' => (float) $item['qty_received'],
            'buy_price' => (float) $item['buy_price'],
            'sale_price' => (float) $item['sale_price'],
        ], $validated['items']);

        $this->warehouseReceiptService->createReceipt($container, [
            'receipt_date' => $validated['receipt_date'],
            'notes' => $validated['notes'] ?? null,
        ], $items);

        return redirect()->route('warehouse-receipts.index')->with('success', __('Warehouse receipt created.'));
    }

    public function show(WarehouseReceipt $warehouseReceipt): Response
    {
        $this->authorize('view', $warehouseReceipt);

        $warehouseReceipt->load(['container', 'receivedByUser', 'receiptItems.product']);

        return Inertia::render('WarehouseReceipts/Show', [
            'receipt' => $warehouseReceipt,
        ]);
    }
}
