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
        $products = Product::orderBy('name')->get();
        $containerId = $request->input('container_id') ? (int) $request->input('container_id') : null;

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
