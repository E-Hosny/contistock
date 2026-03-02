<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function dashboard(): Response
    {
        $supplierBalances = $this->reportService->supplierBalances();
        $customerBalances = $this->reportService->customerBalances();
        $suppliersTotalRemaining = $supplierBalances->sum('balance');
        $customersTotalRemaining = $customerBalances->sum('balance');

        $containers = Container::with('supplier')->get();
        $inventoryValue = 0; // Could be computed from stock * cost if needed
        $profitReport = $this->reportService->profitReport();
        $totalProfit = collect($profitReport['by_container'])->sum('total_sold_profit');

        return Inertia::render('Dashboard', [
            'stats' => [
                'suppliers_balance' => round($suppliersTotalRemaining, 2),
                'customers_balance' => round($customersTotalRemaining, 2),
                'inventory_value' => $inventoryValue,
                'total_profit' => round($totalProfit, 2),
            ],
            'supplierBalances' => $supplierBalances->take(5),
            'customerBalances' => $customerBalances->take(5),
        ]);
    }

    public function containerSummary(Container $container): Response
    {
        $this->authorize('view', $container);

        $summary = $this->reportService->containerSummary($container);

        return Inertia::render('Reports/ContainerSummary', [
            'summary' => $summary,
        ]);
    }

    public function customerBalance(): Response
    {
        $balances = $this->reportService->customerBalances();

        return Inertia::render('Reports/CustomerBalance', [
            'balances' => $balances,
        ]);
    }

    public function supplierBalance(): Response
    {
        $balances = $this->reportService->supplierBalances();

        return Inertia::render('Reports/SupplierBalance', [
            'balances' => $balances,
        ]);
    }

    public function profitReport(Request $request): Response
    {
        $containerId = $request->input('container_id') ? (int) $request->input('container_id') : null;
        $report = $this->reportService->profitReport($containerId);
        $containers = Container::orderBy('product_name')->get(['id', 'product_name']);

        return Inertia::render('Reports/ProfitReport', [
            'report' => $report,
            'containers' => $containers,
            'filterContainerId' => $containerId,
        ]);
    }
}
