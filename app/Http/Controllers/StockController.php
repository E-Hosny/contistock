<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function index(Request $request): Response
    {
        $containerId = $request->input('container_id') ? (int) $request->input('container_id') : null;

        if ($containerId !== null) {
            $linkedIds = $this->stockService->productIdsLinkedToContainer($containerId);
            $products = $linkedIds === []
                ? collect()
                : Product::query()->whereIn('id', $linkedIds)->orderBy('name')->get();
        } else {
            $products = Product::orderBy('name')->get();
        }

        $stock = [];
        foreach ($products as $product) {
            $stock[] = [
                'product' => $product,
                'available_qty' => $this->stockService->availableQty((int) $product->id, $containerId),
            ];
        }

        $containers = Container::orderBy('product_name')->get(['id', 'product_name']);

        return Inertia::render('Stock/Index', [
            'stock' => $stock,
            'containers' => $containers,
            'filterContainerId' => $containerId,
        ]);
    }
}
