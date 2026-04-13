<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContainerExpenseRequest;
use App\Http\Requests\UpdateContainerExpenseRequest;
use App\Models\Container;
use App\Models\ContainerExpense;
use App\Services\ContainerCostService;
use Illuminate\Http\RedirectResponse;

class ContainerExpenseController extends Controller
{
    public function __construct(
        protected ContainerCostService $containerCostService
    ) {}

    public function store(StoreContainerExpenseRequest $request, Container $container): RedirectResponse
    {
        ContainerExpense::create([
            'tenant_id' => $container->tenant_id,
            'container_id' => $container->id,
            'description' => $request->validated('description'),
            'amount' => $request->validated('amount'),
            'notes' => $request->validated('notes'),
        ]);

        $this->containerCostService->refreshTotalCost($container->fresh());

        return redirect()->route('containers.purchases', $container)->with('success', __('Expense added.'));
    }

    public function update(UpdateContainerExpenseRequest $request, Container $container, ContainerExpense $expense): RedirectResponse
    {
        $expense->update($request->validated());

        $this->containerCostService->refreshTotalCost($container->fresh());

        return redirect()->route('containers.purchases', $container)->with('success', __('Expense updated.'));
    }

    public function destroy(Container $container, ContainerExpense $expense): RedirectResponse
    {
        $this->authorize('update', $container);
        abort_unless($container->canEditPurchasePlan(), 403);
        abort_unless((int) $expense->container_id === (int) $container->id, 404);

        $expense->delete();
        $this->containerCostService->refreshTotalCost($container->fresh());

        return redirect()->route('containers.purchases', $container)->with('success', __('Expense removed.'));
    }
}
