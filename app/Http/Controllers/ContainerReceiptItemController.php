<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContainerReceiptItemRequest;
use App\Http\Requests\UpdateContainerReceiptItemRequest;
use App\Models\Container;
use App\Models\ReceiptItem;
use App\Services\ContainerCostService;
use Illuminate\Http\RedirectResponse;

class ContainerReceiptItemController extends Controller
{
    public function __construct(
        protected ContainerCostService $containerCostService
    ) {}

    public function store(StoreContainerReceiptItemRequest $request, Container $container): RedirectResponse
    {
        ReceiptItem::create([
            'tenant_id' => $container->tenant_id,
            'warehouse_receipt_id' => null,
            'container_id' => $container->id,
            'product_id' => $request->validated('product_id'),
            'qty_received' => $request->validated('qty_received'),
            'buy_price' => $request->validated('buy_price'),
            'sale_price' => $request->validated('sale_price'),
        ]);

        $this->containerCostService->refreshTotalCost($container->fresh());

        return redirect()->route('containers.purchases', $container)->with('success', __('Purchase line added.'));
    }

    public function update(UpdateContainerReceiptItemRequest $request, Container $container, ReceiptItem $receiptItem): RedirectResponse
    {
        $receiptItem->update($request->validated());

        $this->containerCostService->refreshTotalCost($container->fresh());

        return redirect()->route('containers.purchases', $container)->with('success', __('Purchase line updated.'));
    }

    public function destroy(Container $container, ReceiptItem $receiptItem): RedirectResponse
    {
        $this->authorize('update', $container);
        abort_unless($container->canEditPurchasePlan(), 403);
        abort_unless((int) $receiptItem->container_id === (int) $container->id, 404);
        abort_unless($receiptItem->warehouse_receipt_id === null, 403);

        $receiptItem->delete();
        $this->containerCostService->refreshTotalCost($container->fresh());

        return redirect()->route('containers.purchases', $container)->with('success', __('Purchase line removed.'));
    }
}
