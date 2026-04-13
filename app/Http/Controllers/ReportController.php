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
        $this->authorize('viewAny', Container::class);

        $containers = Container::with('supplier')->orderByDesc('created_at')->get();
        foreach ($containers as $container) {
            $container->append(['paid_amount', 'remaining_amount']);
        }

        return Inertia::render('Dashboard', [
            'containers' => $containers,
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
