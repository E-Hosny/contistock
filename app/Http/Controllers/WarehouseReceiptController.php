<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseReceiptRequest;
use App\Models\Container;
use App\Models\ReceiptItem;
use App\Models\WarehouseReceipt;
use App\Services\WarehouseReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

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

        $preselectedContainerId = $request->query('container_id') ? (int) $request->query('container_id') : null;

        $eligibleContainers = Container::query()
            ->whereIn('status', ['draft', 'purchased'])
            ->whereHas('receiptItems', fn ($q) => $q->whereNull('warehouse_receipt_id'))
            ->orderBy('product_name')
            ->get(['id', 'product_name']);

        if ($preselectedContainerId && ! $eligibleContainers->firstWhere('id', $preselectedContainerId)) {
            $extra = Container::find($preselectedContainerId);
            if ($extra && $request->user()->can('view', $extra)) {
                $eligibleContainers = collect([$extra->only(['id', 'product_name'])])
                    ->merge($eligibleContainers->map(fn ($c) => $c->only(['id', 'product_name'])))
                    ->unique('id')
                    ->values();
            }
        }

        $plannedItems = collect();
        if ($preselectedContainerId) {
            $selected = Container::find($preselectedContainerId);
            if ($selected && $request->user()->can('view', $selected)) {
                $plannedItems = ReceiptItem::query()
                    ->where('container_id', $selected->id)
                    ->whereNull('warehouse_receipt_id')
                    ->with('product')
                    ->orderBy('id')
                    ->get();
            }
        }

        return Inertia::render('WarehouseReceipts/Create', [
            'eligibleContainers' => $eligibleContainers,
            'preselectedContainerId' => $preselectedContainerId,
            'plannedItems' => $plannedItems,
        ]);
    }

    public function store(StoreWarehouseReceiptRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $container = Container::findOrFail($validated['container_id']);

        $this->authorize('create', WarehouseReceipt::class);
        $this->authorize('view', $container);

        try {
            $receipt = $this->warehouseReceiptService->finalizePlannedReceipt($container, [
                'receipt_date' => $validated['receipt_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('warehouse-receipts.show', $receipt)->with('success', __('Warehouse receipt created.'));
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
