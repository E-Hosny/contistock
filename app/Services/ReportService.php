<?php

namespace App\Services;

use App\Models\Container;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        protected ContainerService $containerService,
        protected SaleService $saleService,
        protected StockService $stockService
    ) {}

    /**
     * Container summary: weight, cost, paid, remaining, received items, sold items, profits.
     */
    public function containerSummary(Container $container): array
    {
        $paid = $this->containerService->paidAmount($container);
        $remaining = $this->containerService->remainingAmount($container);

        $receiptItems = $container->receiptItems;
        $soldQtyByProduct = $container->saleItems()->selectRaw('product_id, sum(qty) as total_qty')->groupBy('product_id')->pluck('total_qty', 'product_id');

        $totalProfitExpected = $receiptItems->sum(fn ($i) => $i->total_profit_expected);
        $totalSoldProfit = $container->saleItems()->sum('profit_line');

        return [
            'container' => $container->load('supplier'),
            'total_weight_kg' => $container->total_weight_kg,
            'total_cost' => (float) $container->total_cost,
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'receipt_items' => $receiptItems,
            'sold_items_summary' => $soldQtyByProduct,
            'total_profit_expected' => $totalProfitExpected,
            'total_sold_profit' => $totalSoldProfit,
        ];
    }

    /**
     * Customer balance: total sales, total paid, balance per customer.
     */
    public function customerBalances(): Collection
    {
        return Customer::all()->map(function (Customer $customer) {
            $totalSales = (float) $customer->sales()->where('status', '!=', 'cancelled')->sum('total');
            $totalPaid = (float) $customer->sales()->where('status', '!=', 'cancelled')->withSum('customerPayments', 'amount')->get()->sum(fn (Sale $s) => (float) ($s->customer_payments_sum_amount ?? 0));

            return [
                'customer' => $customer,
                'total_sales' => $totalSales,
                'total_paid' => $totalPaid,
                'balance' => $totalSales - $totalPaid,
            ];
        });
    }

    /**
     * Supplier balance: total containers cost, total paid, balance per supplier.
     */
    public function supplierBalances(): Collection
    {
        return Supplier::with('containers')->get()->map(function (Supplier $supplier) {
            $totalCost = (float) $supplier->containers->sum('total_cost');
            $totalPaid = (float) $supplier->containers->sum(fn (Container $c) => $this->containerService->paidAmount($c));

            return [
                'supplier' => $supplier,
                'total_cost' => $totalCost,
                'total_paid' => $totalPaid,
                'balance' => $totalCost - $totalPaid,
            ];
        });
    }

    /**
     * Profit report: per container and/or per product.
     */
    public function profitReport(?int $containerId = null): array
    {
        $containers = $containerId
            ? \App\Models\Container::where('id', $containerId)->get()
            : \App\Models\Container::all();

        $byContainer = [];
        foreach ($containers as $container) {
            $summary = $this->containerSummary($container);
            $byContainer[] = [
                'container_id' => $container->id,
                'container_name' => $container->product_name,
                'total_cost' => $summary['total_cost'],
                'total_sold_profit' => $summary['total_sold_profit'],
                'total_profit_expected' => $summary['total_profit_expected'],
            ];
        }

        return [
            'by_container' => $byContainer,
        ];
    }
}
