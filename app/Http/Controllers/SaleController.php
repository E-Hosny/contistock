<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Container;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReceiptItem;
use App\Models\Sale;
use App\Services\SaleService;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService,
        protected StockService $stockService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Sale::class);

        $query = Sale::with('customer');
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }
        $sales = $query->orderByDesc('sale_date')->paginate(15)->withQueryString();

        foreach ($sales as $s) {
            $s->append(['paid_amount', 'remaining_amount']);
        }

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'filters' => $request->only('status', 'customer_id'),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Sale::class);

        $preContainerId = $request->query('container_id') ? (int) $request->query('container_id') : null;
        $containers = Container::where('status', 'received_to_warehouse')->orderBy('product_name')->get(['id', 'product_name']);
        $preselectedContainerId = null;

        if ($preContainerId) {
            $pre = Container::find($preContainerId);
            if ($pre && $request->user()->can('view', $pre) && $pre->status === 'received_to_warehouse') {
                $containers = $containers->sortByDesc(fn ($c) => (int) $c->id === (int) $pre->id)->values();
                $preselectedContainerId = $pre->id;
            }
        }

        return Inertia::render('Sales/Create', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku']),
            'containers' => $containers,
            'preselectedContainerId' => $preselectedContainerId,
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $sale = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $items = [];
            foreach ($validated['items'] as $item) {
                $lineTotal = (float) $item['qty'] * (float) $item['unit_price'];
                $subtotal += $lineTotal;
                $receiptItem = ReceiptItem::where('product_id', $item['product_id'])
                    ->where('container_id', $item['container_id'])
                    ->whereNotNull('warehouse_receipt_id')
                    ->orderByDesc('id')
                    ->first();
                $buyPrice = $receiptItem ? (float) $receiptItem->buy_price : 0;
                $profitLine = ($item['unit_price'] - $buyPrice) * $item['qty'];
                $items[] = [
                    'product_id' => $item['product_id'],
                    'container_id' => $item['container_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'buy_price_snapshot' => $buyPrice,
                    'line_total' => $lineTotal,
                    'profit_line' => $profitLine,
                ];
            }
            $discount = (float) ($validated['discount'] ?? 0);
            $total = $subtotal - $discount;

            $sale = Sale::create([
                'tenant_id' => \current_tenant_id(),
                'customer_id' => $validated['customer_id'],
                'sale_date' => $validated['sale_date'],
                'status' => 'draft',
                'invoice_no' => $validated['invoice_no'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
            ]);

            foreach ($items as $item) {
                $sale->saleItems()->create(array_merge($item, ['tenant_id' => \current_tenant_id()]));
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', __('Sale created.'));
    }

    public function show(Sale $sale): Response
    {
        $this->authorize('view', $sale);

        $sale->load(['customer', 'saleItems.product', 'saleItems.container', 'customerPayments']);
        $sale->append(['paid_amount', 'remaining_amount']);

        return Inertia::render('Sales/Show', [
            'sale' => $sale,
        ]);
    }

    public function edit(Sale $sale): Response
    {
        $this->authorize('update', $sale);

        if ($sale->status !== 'draft') {
            return redirect()->route('sales.show', $sale)->with('error', __('Only draft sales can be edited.'));
        }

        $sale->load('saleItems');

        return Inertia::render('Sales/Edit', [
            'sale' => $sale,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku']),
            'containers' => Container::where('status', 'received_to_warehouse')->orderBy('product_name')->get(['id', 'product_name']),
        ]);
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        if ($sale->status !== 'draft') {
            return redirect()->back()->with('error', __('Only draft sales can be edited.'));
        }

        $validated = $request->validated();

        DB::transaction(function () use ($sale, $validated) {
            $sale->update([
                'customer_id' => $validated['customer_id'],
                'sale_date' => $validated['sale_date'],
                'invoice_no' => $validated['invoice_no'] ?? null,
                'discount' => $validated['discount'] ?? 0,
            ]);

            if (isset($validated['items'])) {
                $sale->saleItems()->delete();
                $subtotal = 0;
                foreach ($validated['items'] as $item) {
                    $lineTotal = (float) $item['qty'] * (float) $item['unit_price'];
                    $subtotal += $lineTotal;
                    $receiptItem = ReceiptItem::where('product_id', $item['product_id'])
                        ->where('container_id', $item['container_id'])
                        ->whereNotNull('warehouse_receipt_id')
                        ->orderByDesc('id')
                        ->first();
                    $buyPrice = $receiptItem ? (float) $receiptItem->buy_price : 0;
                    $sale->saleItems()->create([
                        'tenant_id' => \current_tenant_id(),
                        'product_id' => $item['product_id'],
                        'container_id' => $item['container_id'],
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'buy_price_snapshot' => $buyPrice,
                        'line_total' => $lineTotal,
                        'profit_line' => ($item['unit_price'] - $buyPrice) * $item['qty'],
                    ]);
                }
                $sale->update([
                    'subtotal' => $subtotal,
                    'discount' => $validated['discount'] ?? 0,
                    'total' => $subtotal - ($validated['discount'] ?? 0),
                ]);
            }
        });

        return redirect()->route('sales.show', $sale)->with('success', __('Sale updated.'));
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $this->authorize('delete', $sale);

        if ($sale->status === 'confirmed') {
            return redirect()->back()->with('error', __('Cannot delete a confirmed sale.'));
        }

        $sale->delete();

        return redirect()->route('sales.index')->with('success', __('Sale deleted.'));
    }

    public function confirm(Sale $sale): RedirectResponse
    {
        $this->authorize('update', $sale);

        if ($sale->status !== 'draft') {
            return redirect()->back()->with('error', __('Sale is already confirmed or cancelled.'));
        }

        try {
            $this->saleService->confirm($sale);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', __('Sale confirmed.'));
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        $this->authorize('update', $sale);

        $this->saleService->cancel($sale);

        return redirect()->back()->with('success', __('Sale cancelled.'));
    }
}
