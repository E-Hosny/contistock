<?php

namespace App\Http\Requests;

use App\Models\Container;
use App\Models\ReceiptItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContainerReceiptItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $container = $this->route('container');

        return $container instanceof Container
            && $this->user()->can('update', $container)
            && $container->canEditPurchasePlan();
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->where(fn ($q) => $q->where('tenant_id', $this->user()->tenant_id)),
            ],
            'qty_received' => ['required', 'numeric', 'min:0.01'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Container $container */
            $container = $this->route('container');
            $productId = (int) $this->input('product_id');
            if (! $productId) {
                return;
            }
            $exists = ReceiptItem::query()
                ->where('container_id', $container->id)
                ->where('product_id', $productId)
                ->whereNull('warehouse_receipt_id')
                ->exists();
            if ($exists) {
                $validator->errors()->add('product_id', __('This product is already on the purchase list for this container.'));
            }
        });
    }
}
